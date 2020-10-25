<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-21
 * Time: 14:24
 */

namespace App\Http\Controllers\Api;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Account;
use App\Model\Artbc\BtScoreUnlock;
use App\Model\Artbc\WalletInvite;
use App\Model\Btshop\Aliaccount;
use App\Model\Btshop\AlipayDraw;
use App\Model\Btshop\AlipayTn;
use App\Model\Btshop\Bankcard;
use App\Model\Btshop\BtshopOrder;
use App\Model\Finance;
use App\Model\FinanceType;
use App\Model\Member;
use App\Model\Profile;
use App\Model\Vip;
use App\Service\AccountService;
use App\Service\AliPaySdkService;
use App\Service\FinanceService;
use App\Service\SmsService;
use App\Service\SsoService;
use App\Service\ValidatorService;
use App\Utils\ApiResUtil;
use App\Utils\DateUtil;
use App\Utils\RedisKeys;
use App\Utils\RedisUtil;
use App\Utils\ResUtil;
use App\Utils\SmsUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlipayController extends Controller
{
    public function bind(Request $request, ValidatorService $validatorService)
    {
        $data = $request->all();
        $validator = $validatorService->checkValidator([
            'account' => 'required|string',
            'name' => 'required|string',
        ], $data);
        if ($validator['code'] !== 200){
            return ApiResUtil::error($validator['data']);
        }
        $member = Member::apiCurrent();
        DB::beginTransaction();
        try {
            $account = Aliaccount::fetchWithAccount($data['account']);
            if ($account && $account->member_id !== $member->id){
                throw new TradeException('该支付宝已被其他用户绑定');
            }
            $model = Aliaccount::fetchModel($member->id);

            if (!$model){
                $model = new Aliaccount();
            }
            $model->member_id = $member->id;
            $model->account = $data['account'];
            $model->name = $data['name'];
            if(!$model->save()){
                throw new TradeException('绑定失败');
            }
            DB::commit();
            return ApiResUtil::ok();
        }catch (TradeException $e){
            DB::rollBack();
            return ApiResUtil::error($e->getMessage());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return ApiResUtil::error($e->getMessage());
        }
    }

    public function index()
    {
        $member = Member::apiCurrent();
        $model = Aliaccount::fetchModel($member->id);
        $data = $model ? $model->toArray() : [];
        return ApiResUtil::ok($data);
    }

    public function tn()
    {
        $models = AlipayTn::select('tn', 'fee')->get();
        return ApiResUtil::ok($models);
    }

    /**
     * @param Request $request
     */
    public function draw(Request $request, AccountService $accountService, AliPaySdkService $aliPaySdkService)
    {
        /**
         * 1、判断数量是否正确
         * 1.5、验证短信码
         * 2、验证余额是否足够
         * 3、单人每天限额5000
         * 4、调取支付宝接口转账
         * 5、成功写入状态
         *      失败，写入待处理状，查询订单
         * 6、提交事务
         */
//        return ApiResUtil::error('联调中');
        $h = date('H');
        if ($h < 10 || $h >= 17){
            return ApiResUtil::error('支付宝提现时间为每天10-17时！');
        }
        $amount = intval($request->get('amount'));
        $verifycode = $request->get('verifycode');
        if (empty($amount) || !is_numeric($amount) || $amount < 100 || $amount > 1000){
            return ApiResUtil::error('数量应为100-1000，50的整数倍');
        }

        if ($amount % 50 != 0){
            return ApiResUtil::error('提现数量应为50的整数倍');
        }

        $member = Member::apiCurrent();
        if (env('APP_ENV', 'prod') !== 'prod' && !in_array($member->phone, [
                '13800138001', '15001204748', '13659828348', '18611331597', '15500005514'
            ])){
            return ApiResUtil::error('提现功能尚未开通。');
        }

        if (Vip::isVip($member->phone)){
            return ApiResUtil::error('您的账户暂不支持支付宝提现，请使用银行卡提现。');
        }

        $profile = Profile::fetchByMid($member->id);

        // 提现频次限制，一分钟只允许一次
        $key = RedisKeys::API_PER_TIMES_LIMIT_PRE . 'alipay:draw:' . $member->id;
        if (RedisUtil::get($key) == 1){
            return ApiResUtil::error('提现过于频繁，请稍候再试');
        }
        RedisUtil::set($key,1, 60);

        $verify = SsoService::smsVerify($member->phone, $member->nationcode, $verifycode, 1);

        if ($verify['code'] !== 0){
            return ApiResUtil::error($verify['data']);
        }

        // 非 13829279848. 13292815167 旗下，提现失败
        if (!WalletInvite::where('member_id', $member->id)
            ->whereIn('pid', [2541, 2302, 2697, 2528, 10609, 2485])->exists()){
            return ApiResUtil::error('转账失败: 单日转账额度已满');
        }

//        if (WalletInvite::where('member_id', $member->id)->where('pid', 2485)->exists()){
//            if ($member->created_at < '2019-02-25'){
//                return ApiResUtil::error('转账失败: 单日转账额度已满');
//            }
//        }

        return ApiResUtil::error('转账失败: 单日转账额度已满');
        if (AlipayDraw::where('created_at', '>', DateUtil::today())->sum('amount') > 100) {
            return ApiResUtil::error('转账失败: 单日转账额度已满');
        }

        if (!WalletInvite::where('member_id', $member->id)->where('pid', 2485)->exists()){
            if (BtshopOrder::paytypeAmount($member->id) > 40000) {
                return ApiResUtil::error('支付系统对接调整中');
            }

            if (BtScoreUnlock::adminUnlockAmount($member->id) > 0){
                return ApiResUtil::error('支付系统对接调整中');
            }
        }



        try{
            DB::beginTransaction();
            // 检查现金余额
            $balance = $accountService->balance($member->id);
            if($balance < $amount) {
                throw new TradeException('可用现金不足');
            }
            // 检查绑定账户
            $alipayAccount = Aliaccount::fetchModel($member->id);
            if (!$alipayAccount) {
                throw new TradeException('尚未绑定支付宝账号');
            }
            $limit = 1000;
            if (WalletInvite::where('member_id', $member->id)->where('pid', 2485)->exists()){
                $limit = 500;
            }
            // redis 检查单日额度
            if (AlipayDraw::todayMemberAmount($member->id) + $amount > 5000) {
                throw new TradeException('每个用户单日提现额度为5000');
            }
            if (AlipayDraw::todayAliaccountAmount($alipayAccount->account) + $amount > $limit) {
                throw new TradeException('单日提现额度为'.$limit);
            }
//            if ($profile && AlipayDraw::todayIdnoAmount($profile->idno) + $amount > $limit) {
//                throw new TradeException('每个认证身份每日提现额度为'.$limit);
//            }
            // 单日总额度
            if (Member::todayDrawAmount($member->id)  + $amount > 5000) {
                throw new TradeException('单日提现总额度为5000');
            }
            // 检查单日额度
            $todayAmount = AlipayDraw::todayAmount($member->id);
            if ($todayAmount + $amount > $limit){
                throw new TradeException('单日提现额度为'.$limit);
            }
            $accountTodayAmount = AlipayDraw::accountTodayAount($alipayAccount->account);
            if ($accountTodayAmount + $amount > $limit){
                throw new TradeException('单支付宝账号单日提现额度为'.$limit);
            }
            // 生成订单号
            $order_no = date('Ymdh') . rand(100000, 999999);
            // 扣除现金余额
            $accountId = $accountService->getAccountId($member->id);
            $accountService->addAsset($accountId , Account::BALANCE, '-' . $amount, '');
            $r = FinanceService::record($member->id, Account::BALANCE, Finance::WALLET_ALIPAY_DRAW,
                -1 * $amount, 0, '提现到支付宝,金额:'.$amount.'元', $order_no);
            if(!$r) {
                throw new \Exception('提现失败');
            }

            $alidraw = [
                'member_id' => $member->id,
                'order_no' => $order_no,
                'amount' => $amount,
                'account' => $alipayAccount->account
            ];
            $alidrawModel = new AlipayDraw();

            $alidrawModel->fill($alidraw);
            if(!$alidrawModel->save()){
                throw new TradeException('提现失败，请稍后重试');
            }

            // 调取支付宝转账
            Log::debug('order_no: ' . $order_no);
            $result = $aliPaySdkService->withdrawal($order_no, $amount, $alipayAccount->account, $alipayAccount->name);
            $resultCode = $result->code;

            Log::debug('ali transfer return', [
                'result' => $result
            ]);

            $back_data =  [
                'code' => $resultCode,
                'msg' => $result->msg,
                'out_biz_no' => '',
                'order_id'=>'',
                'pay_date'=>'',
                'sub_code'=>'',
                'sub_msg'=>''
            ];

            if(!empty($resultCode) && $resultCode ==  10000){
                $back_data['out_biz_no'] = $result->out_biz_no;
                $back_data['order_id'] = $result->order_id;
                $back_data['pay_date'] = $result->pay_date;
                $alidraw['stat'] = AlipayDraw::STAT_DONE;
            } elseif ($resultCode == 20000 || $resultCode == 40004){
                // 如果不成功，调取 alipay.fund.trans.order.query 查询转账状态
                if (!$aliPaySdkService->orderQuery($order_no, isset($result->order_id) ? $result->order_id : '')){

                    $result = $aliPaySdkService->withdrawal($order_no, $amount, $alipayAccount->account, $alipayAccount->name);
                    $resultCode = $result->code;
                    $back_data =  [
                        'code' => $resultCode,
                        'msg' => $result->msg,
                        'out_biz_no' => '',
                        'order_id'=>'',
                        'pay_date'=>'',
                        'sub_code'=>'',
                        'sub_msg'=>''
                    ];
                    if(!empty($resultCode) && $resultCode ==  10000){
                        $back_data['out_biz_no'] = $result->out_biz_no;
                        $back_data['order_id'] = $result->order_id;
                        $back_data['pay_date'] = $result->pay_date;
                        $alidraw['stat'] = AlipayDraw::STAT_DONE;
                    } elseif ($resultCode == 20000 || ($resultCode == 40004 && $result->sub_code == 'SYSTEM_ERROR')){
                        if ($aliPaySdkService->orderQuery($order_no, isset($result->order_id) ? $result->order_id : '')){
                            $alidraw['stat'] = AlipayDraw::STAT_DONE;
                        }else{
                            $alidraw['stat'] = AlipayDraw::STAT_DOING;
                        }
                    }else{
                        Log::error($back_data);
                        throw new TradeException('转账失败: 单日最多可转100万');
                    }
                }
            } else {
                $back_data['sub_code'] = $result->sub_code;
                $back_data['sub_msg'] = $result->sub_msg;
                Log::error($back_data);
                throw new TradeException('转账失败: 单日最多可转100万');
            }
            DB::commit();
            // redis 记录今日提现额度
            AlipayDraw::todayMemberIncrby($member->id, intval($amount));
            AlipayDraw::todayAliaccountAIncrby($alipayAccount->account, intval($amount));
//            $profile && AlipayDraw::todayIdnoIncrby($profile->idno, $amount);
            $alidrawModel->exdata = json_encode($back_data);
            $alidrawModel->save();
            return ApiResUtil::ok('提现完成，请注意查收！');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (!$e instanceof TradeException){
                \Log::error($_REQUEST);
                \Log::error($e->getTraceAsString());
                RedisUtil::lpush(RedisKeys::WARING_EMAIL_LIST, $e->getMessage());
                $msg = '提现失败，请稍候重试';
            }
            Log::debug($e->getMessage());
            DB::rollBack();
            return ApiResUtil::error($msg);
        }

    }

}