<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-06
 * Time: 20:25
 */

namespace App\Http\Controllers\Disapi;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Artbc;
use App\Model\Btshop\BlockAssetExchangeLog;
use App\Model\Btshop\BlockAssetLog;
use App\Model\Btshop\BlockAssetType;
use App\Model\Btshop\BlockRechargeLog;
use App\Model\Cms\Push;
use App\Model\Member;
use App\Utils\ApiResUtil;
use App\Utils\DisVerify;
use App\Utils\RedisKeys;
use App\Utils\RedisUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Api;

class ArtbcController extends Controller
{
    public function price()
    {
        return ApiResUtil::ok([
           'price' => Artbc::giftRate()
        ]);
    }

    /**
     * 调取接口兑换积分为 ARTTBC
     * 1. 验证ticket
     * 2. 添加ARTTBC
     * 3. 判断是否大于10000，是加入审核状态 todo
     * @param Request $request
     * @return array
     */
    public function draw(Request $request)
    {
        return ApiResUtil::error('系统升级，暂停服务');
        $amount = $request->get('amount');
        $price = $request->get('price');
        $order_code = $request->get('order_code');
        $ticket = $request->get('ticket');
        $phone = $request->get('phone');
        if (empty($amount) || $amount < 0 || !is_numeric($amount)){
            return ApiResUtil::error('参数格式不正确');
        }

        if (empty($price) || !is_numeric($price) || empty($order_code)){
            return ApiResUtil::error(ApiResUtil::WRONG_PARAMS);
        }

//        if ($ticket != 'LDmEIEkkDi2233') {
//            return ApiResUtil::error('wrong ticket');
//        }

        $member = DisVerify::verifyTk($ticket);
//        $member = Member::fetchModelByPhone($phone);
        if (!$member){
            return ApiResUtil::error('身份认证失败');
        }

        // 兑换次限制，一分钟只允许一次
        $key = RedisKeys::API_PER_TIMES_LIMIT_PRE . 'dis:arttbc:draw:' . $member->id;
        if (RedisUtil::get($key) == 1){
            return ApiResUtil::error('操作过于频繁，请稍候再试');
        }
        RedisUtil::set($key,1, 60);

        \DB::beginTransaction();
        try{
            if (BlockAssetExchangeLog::orderExist($order_code)){
                throw new TradeException('订单已存在');
            }
            $btconfig = Artbc\BtConfig::fetchOne();
            $artbcPrice = $btconfig ? $btconfig->price : Artbc\BtConfig::getPrice();
            Log::debug('price: ' . $artbcPrice);
            if ($price != 3){
                throw new TradeException('价格不正确');
            }
            // 添加 exchage log
            BlockAssetExchangeLog::add($member->id, BlockAssetType::CODE_ARTTBC, $amount, $order_code, $price);
            BlockAssetLog::record($member->id, BlockAssetType::CODE_ARTTBC, $amount, BlockAssetLog::TYPE_EXCHANGE);
            DB::commit();
            return ApiResUtil::ok();
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return ApiResUtil::error($e->getMessage());
        }
    }

    public function scores(Request $request)
    {
        $page = $request->get('page', 0);
        $models = Artbc\BtScoreUnlock::orderBy('id')
            ->offset($page * 100)
            ->limit(100)
            ->get();
        $list = [];
        foreach ($models as $model)
        {
            if (empty($model->order_code)){
                $model->order_code = Artbc\BtScoreUnlock::orderMake();
                $model->save();
            }
            $list[] = [
                'phone' => $model->member->phone,
                'order_code' => $model->order_code,
                'amount' => $model->amount,
                'unlocked_amount' => $model->unlocked_amount,
                'created_at' => explode(' ', $model->created_at)[0]
            ];
        }
        return ApiResUtil::ok([
            'hasMore' => intval(count($list) == 100),
            'list' => $list
        ]);
    }
}