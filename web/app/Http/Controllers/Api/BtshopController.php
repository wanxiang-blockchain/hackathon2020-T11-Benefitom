<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2018-12-23
 * Time: 10:56
 */

namespace App\Http\Controllers\Api;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Account;
use App\Model\Artbc\BtScoreUnlock;
use App\Model\Btshop\BtshopDelivery;
use App\Model\Btshop\BtshopOrder;
use App\Model\Btshop\BtshopProduct;
use App\Model\Finance;
use App\Model\ListModel;
use App\Model\Member;
use App\Service\AccountService;
use App\Service\FinanceService;
use App\Service\ValidatorService;
use App\Utils\ApiResUtil;
use App\Utils\BtshopUtil;
use App\Utils\OssUtil;
use function Composer\Autoload\includeFile;
use function Couchbase\basicEncoderV1;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Twilio\Rest\Api;

class BtshopController extends Controller
{
    public function products()
    {
        $models = BtshopProduct::where('enable', BtshopProduct::ENABLED)
            ->get();
        foreach ($models as &$model) {
            $model->img = OssUtil::fetchGetSignUrl($model->img);
            $model->finalPrice = $model->getAttributeFinalPrice();
        }
        return ApiResUtil::ok([
            'list' => $models
        ]);
    }

    public function product()
    {
        $id = request()->get('id');
        if (!$id){
            return ApiResUtil::error(ApiResUtil::WRONG_PARAMS);
        }
        $model = BtshopProduct::fetchEnabelModel($id, ['id', 'name', 'img', 'price', 'score', 'per_limit', 'paytype', 'bt_price', 'rmb_price', 'artbcs_price']);
        if (!$model){
            return ApiResUtil::error(ApiResUtil::NO_DATA);
        }
        $model->finalPrice = $model->getAttributeFinalPrice();
        $model->img = OssUtil::fetchGetSignUrl($model->img);
        return ApiResUtil::ok($model->toArray());
    }

    public function orderMake(Request $request, ValidatorService $validatorService, AccountService $accountService)
    {
        return 404;
        $data = $request->all();
        $validator = $validatorService->checkValidator(
            [
                'id' => 'required|numeric',
                'amount' => 'required|numeric',
                'paytype' => 'required|in:0,1,2'
            ],
            $data);
        if ($validator['code'] != 200) {
            return ApiResUtil::error($validator['data']);
        }
        if ($data['amount'] <= 0) {
            return ApiResUtil::error('购买数量不正确');
        }
        try{
            \DB::beginTransaction();
            $id = $data['id'];
            $amount = $data['amount'];
            $model = BtshopProduct::fetchEnabelModel($id);
            if (!$model) {
                throw new TradeException('商品不存在');
            }
            $member = Member::apiCurrent();
            // 判断当天购买数量
            $todayBoughtSum = BtshopOrder::todayBoughtCount($member->id, $model->id);
            if ($amount + $todayBoughtSum > $model->per_limit){
                throw new TradeException('每天购买数量不得大于' . $model->per_limit);
            }
            $orderNum = date('YmdH') . randStr(8, 'NUMBER');
            $txdata = randStr(32, 'NUMBER');
            $balance = $accountService->balance($member->id);
            if(in_array($model->paytype, [BtshopProduct::PAYTYPE_RMN, BtshopProduct::PAYTYPE_ARTBC] ) && $balance < $model->rmb_price) {
                throw new TradeException('现金余额不足');
            }
            if (!BtshopOrder::add($orderNum, $model->id, $model->price, $amount, $model->score, $data['paytype'], $txdata, $member->id)) {
                throw new TradeException('服务器异常');
            }
            DB::commit();
            return ApiResUtil::ok([
                'order_num' => $orderNum,
                'price' => $model->price,
                'score' => $model->score,
                'per_limit' => $model->per_limit,
                'txdata' => $txdata
            ]);
        }catch (TradeException $e) {
            \DB::rollBack();
            return ApiResUtil::error($e->getMessage());
        }
    }

    public function orderDone(Request $request, ValidatorService $validatorService, AccountService $accountService)
    {
        return 404;
        $data = $request->all();
        $validator = $validatorService->checkValidator(
            [
                'order_num' => 'required|string',
                'tx' => 'required|string',
            ],
            $data);
        if ($validator['code'] != 200) {
            return ApiResUtil::error($validator['data']);
        }
        try{
            DB::beginTransaction();
            $member = Member::apiCurrent();
            $balance = $accountService->balance($member->id);

            $model = BtshopOrder::fetchModelByOrderNum($data['order_num']);
            if (BtshopOrder::isTxExist($data['tx'])) {
                throw new TradeException('交易号异常');
            }
            if (!$model) {
                throw new TradeException('订单不存在');
            }
            if ($model->stat !== BtshopOrder::STAT_INIT) {
                throw new TradeException('订单已处理，不需重得提交');
            }
            $product = BtshopProduct::find($model->product_id);
            if(in_array($product->paytype, [BtshopProduct::PAYTYPE_RMN, BtshopProduct::PAYTYPE_ARTBC] ) && $balance < $product->rmb_price * $model->amount) {
                throw new TradeException('可用现金不足');
            }
            // 如需支付人民币
            if(in_array($product->paytype, [BtshopProduct::PAYTYPE_RMN, BtshopProduct::PAYTYPE_ARTBC] )){
                $accountId = $accountService->getAccountId($member->id);
                $cost = $product->rmb_price * $model->amount;
                $accountService->addAsset($accountId, Account::BALANCE, '-' . $cost, '');

                $r = FinanceService::record($member->id, Account::BALANCE, Finance::WALLET_SALE_COST,'-'.$cost,
                    0, '艺行派购买商品,金额:'.$cost.'元');
                if(!$r) {
                    throw new TradeException('扣款失败');
                }
            }

            $model->tx = $data['tx'];
            // 法币交易，直接完成
            if ($product->paytype === BtshopProduct::PAYTYPE_RMN) {
                BtScoreUnlock::inviteAdd($member->id, $product->score * $model->amount, $data['order_num']);
                $model->stat = BtshopOrder::STAT_DONE;
            }else{
                $model->stat = BtshopOrder::STAT_TX;
                Redis::lpush(BtshopUtil::ORDER_TX_QUEUE_KEY, $model->order_num);
            }
            if (!$model->save()){
                throw new TradeException('服务器异常, 请稍等再试');
            }
            DB::commit();
            return ApiResUtil::ok();
        }catch (TradeException $e){
            \DB::rollBack();
            return ApiResUtil::error($e->getMessage());
        }
    }

    public function orders(Request $request, ValidatorService $validatorService)
    {
        $data = $request->all();
        $validator = $validatorService->checkValidator(
            [
                'stat' => 'required|in:0,1,2',
            ],
            $data);
        if ($validator['code'] != 200) {
            return ApiResUtil::error($validator['data']);
        }
        $member = Member::apiCurrent();
        $stat = $data['stat'];
        /**
         * "order_num": 17,
        "stat": 1,  // 0待提货，1待发货，2已发货
        "name": "商品名称",
        "img": "https://sadf", // 图片
        "price": 124,  // ARTTBC 价格
        "rmb_price": 124, // 人民币价格
        "bt_price": 228, // ARTTBC 价格
        "paytype": 1,   // 1 ArTBC + rmn 支付，0 ARTTBC支付, 2纯人民币
        "score": 1000,  // 单幅奖励积分
        "amount": 1,  // 购买数量
        "created_at": "2018-11-12 00:00:11",  // 下单时间
        "note": "备注",
        "receive_addr": "收货地址",
        "receive_province": "收货省",
        "receive_city": "收货城市",
        "receive_area": "收货区县",
        "receive_name": "收货人",
        "receive_phone": "收货手机号"
         */
        $list = [];
        if (in_array($stat, [0])){
            // 支付中，从btshop_orders中取数据
            $query = BtshopOrder::where('member_id', $member->id)
                ->where('is_deliveried', 0)
                ->where('amount', '>', 0);
            $query_stat = BtshopOrder::STAT_DONE;
            $models = $query->where('stat', $query_stat)
                ->orderByDesc('id')
                ->get();
            foreach ($models as $i => $model){
                $list[$i] = [
                    'order_num' => $model->order_num,
                    'stat' => 0,
                    'name' => $model->product->name,
                    'img' => OssUtil::fetchGetSignUrl($model->product->img),
                    'price' => $model->product->price,
                    'rmb_price' => $model->product->rmb_price,
                    'bt_price' => $model->product->bt_price,
                    'finalPrice' => $model->product->getAttributeFinalPrice(),
                    'paytype' => $model->product->paytype,
                    'score' => $model->product->score,
                    'amount' => $model->amount,
                    'created_at' => $model->created_at->toDateTimeString(),
                    'note' => '',
                    'receive_addr' => '',
                    'receive_province' => '',
                    'receive_city' => '',
                    'receive_area' => '',
                    'receive_phone' => '',

                ];
            }
        }else{
            $models = BtshopDelivery::where('member_id', $member->id)
                ->where('stat', $stat)
                ->orderByDesc('id')
                ->get();
            foreach ($models as $i => $model){
                $list[$i] = [
                    'order_num' => $model->order_num,
                    'stat' => $model->stat,
                    'name' => $model->product->name,
                    'img' => OssUtil::fetchGetSignUrl($model->product->img),
                    'price' => $model->product->price,
                    'rmb_price' => $model->product->rmb_price,
                    'bt_price' => $model->product->bt_price,
                    'paytype' => $model->product->paytype,
                    'finalPrice' => $model->product->getAttributeFinalPrice(),
                    'score' => $model->product->score,
                    'amount' => $model->order->amount,
                    'created_at' => $model->order->created_at->toDateTimeString(),
                    'note' => $model->note,
                    'receive_addr' => '',
                    'receive_province' => '',
                    'receive_city' => '',
                    'receive_area' => '',
                    'receive_phone' => '',

                ];
            }
        }
        return ApiResUtil::ok([
            'list' => $list
        ]);
    }

    public function order(Request $request){
        $order_num = $request->get('order_num');
        if (empty($order_num)) {
            return ApiResUtil::error(ApiResUtil::NO_DATA);
        }
        $order = BtshopOrder::fetchModelByOrderNum($order_num);
        if (!$order){
            return ApiResUtil::error(ApiResUtil::NO_DATA);
        }
        if ($order->is_deliveried){
            $orderDelivery = $order->orderDelivery;
            $deliveryInfo = ['note', 'receive_phone', 'receive_area', 'receive_city', 'receive_province', 'receive_addr', 'receive_name'];
            foreach ($deliveryInfo as $value){
                $order->$value = $orderDelivery ? $orderDelivery->$value : '';
            }
        }
        $product = $order->product;
        $data = [
            'name' => $product->name,
            'img' => OssUtil::fetchGetSignUrl($product->img),
            'price' => $product->price,
            'bt_price' => $product->bt_price,
            'rmb_price' => $product->rmb_price,
            'finalPrice' => $product->getAttributeFinalPrice(),
            'note' => $order->note,
            'statLabel' => $order->is_deliveried == 0 ? '待提货' : BtshopDelivery::statLabel($orderDelivery->stat),
            'stat' =>  $order->is_deliveried == 0 ? 0 : $orderDelivery->stat,
            'amount' => $order->amount,
            'paytype' => $product->paytype,
            'created_at' => $order->created_at->toDateTimeString()
        ];
        return ApiResUtil::ok($data);

    }
    public function orderTi(Request $request, ValidatorService $validatorService)
    {
        /**
         * receiver           | string | 1  |    |
        receive_addr       | string | 1  |    |
        receive_province   | string | 1  |    |
        receive_city       | string | 1  |    |
        receive_area       | string | 1  |    |
        receive_phone      | string | 1  |    |
        receive_nationcode | string | 1  |    |
         */
//        return ApiResUtil::error('提货服务暂停');
        $data = $request->all();
        $validator = $validatorService->checkValidator(
            [
                'receiver' => 'required',
                'receive_addr' => 'required',
                'receive_province' => 'required',
                'receive_city' => 'required',
                'receive_area' => 'required',
                'receive_phone' => 'required',
                'receive_nationcode' => 'required',
                'order_num' => 'required'
            ],
            $data);
        if ($validator['code'] != 200) {
            return ApiResUtil::error($validator['data']);
        }
        try{
            DB::beginTransaction();
            $member = Member::apiCurrent();
            $model = BtshopOrder::fetchModelByOrderNum($data['order_num']);
            if (!$model || $model->member_id !== $member->id){
                throw new TradeException('订单不存在');
            }
            if (BtshopDelivery::isOrderNumExist($data['order_num'])) {
                throw new TradeException('重复提货');
            }
            $model->is_deliveried = 1;
            $model->save();

            if (!BtshopDelivery::create([
                'member_id' => $model->member_id,
                'order_num' => $model->order_num,
                'stat' => 1,
                'receiver' => $data['receiver'],
                'receive_addr' => $data['receive_addr'],
                'receive_province' => $data['receive_province'],
                'receive_city' => $data['receive_city'],
                'receive_area' => $data['receive_area'],
                'receive_phone' => $data['receive_phone'],
                'receive_nationcode' => $data['receive_nationcode'],
                'product_id' => $model->product_id
            ])){
                throw new TradeException('服务器异常，请稍等再试');
            }
            DB::commit();
            return ApiResUtil::ok();
        }catch (TradeException $e){
            \DB::rollBack();
            return ApiResUtil::error($e->getMessage());
        }
    }
}