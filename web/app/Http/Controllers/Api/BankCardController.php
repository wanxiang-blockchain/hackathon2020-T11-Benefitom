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
use App\Model\Btshop\AlipayDraw;
use App\Model\Btshop\Bankcard;
use App\Model\Btshop\BankDraw;
use App\Model\Finance;
use App\Model\Member;
use App\Model\Profile;
use App\Service\AccountService;
use App\Service\FinanceService;
use App\Service\SsoService;
use App\Service\ValidatorService;
use App\Utils\ApiResUtil;
use App\Utils\RedisKeys;
use App\Utils\RedisUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Api;

class BankCardController extends Controller
{
    public function bind(Request $request, ValidatorService $validatorService)
    {
        $data = $request->all();
        $validator = $validatorService->checkValidator([
            'card' => 'required|string',
            'name' => 'required|string',
            'bank' => 'required|string',
            'headbank' => 'required|string'
        ], $data);
        if ($validator['code'] !== 200){
            return ApiResUtil::error($validator['data']);
        }
        $member = Member::apiCurrent();
        DB::beginTransaction();
        try {
            $card = Bankcard::fetchWithCard($data['card']);
            if ($card && $card->member_id !== $member->id){
                throw new TradeException('该卡号已被其他用户绑定');
            }
            $model = Bankcard::fetchModel($member->id);
            if (!$model){
                $model = new Bankcard();
            }
            $model->member_id = $member->id;
            $model->card = $data['card'];
            $model->name = $data['name'];
            $model->bank = $data['bank'];
            $model->headbank = $data['headbank'];
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
        $model = Bankcard::fetchModel($member->id);
        $data = $model ? $model->toArray() : [];
        return ApiResUtil::ok($data);
    }

    public function draw(Request $request, AccountService $accountService)
    {
        /**
         * 1、判断数量是否正确
         * 1.5、验证短信码
         * 2、验证余额是否足够
         * 3、单人每天限次数5次
         * 4、写入状态
         * 5、成功写入状态
         *      失败，写入待处理状，查询订单
         * 6、提交事务
         */
        return ApiResUtil::error('该功能已下线');
//        return ApiResUtil::error('与文交所对接联调中，提现功能暂关闭！');
        $amount = $request->get('amount');
        $verifycode = $request->get('verifycode');
        if (empty($amount) || !is_numeric($amount) || $amount < 100){
            return ApiResUtil::error('数量不正确');
        }
//        if ($amount < 1000) {
//            return ApiResUtil::error('银行卡单笔提现不得少于1000');
//        }
        if ($amount % 100 != 0){
            return ApiResUtil::error('提现数量应为100的整数倍');
        }
        $member = Member::apiCurrent();

//        $profile = Profile::fetchByMid($member->id);
//        if (empty($profile)) {
//            return ApiResUtil::error('您尚未做身份认证，请先进行身份认证');
//        }

        // 提现频次限制，一分钟只允许一次
        $key = RedisKeys::API_PER_TIMES_LIMIT_PRE . 'bankcard:draw:' . $member->id;
        if (RedisUtil::get($key) == 1){
            return ApiResUtil::error('提现过于频繁，请稍候再试');
        }
        RedisUtil::set($key,1, 60);

        $verify = SsoService::smsVerify($member->phone, $member->nationcode, $verifycode, 1);

        if ($verify['code'] !== 0){
            return ApiResUtil::error($verify['data']);
        }

        DB::beginTransaction();
        try{
            // 检查现金余额
            $balance = $accountService->balance($member->id);
            if($balance < $amount) {
                throw new TradeException('可用现金不足');
            }
            // 检查绑定账户
            $bankAccount = Bankcard::fetchModel($member->id);
            if (!$bankAccount) {
                throw new TradeException('尚未绑定银行卡');
            }
            $todayTimes = BankDraw::todayDrawTimes($member->id);
            if ($todayTimes >= 1){
                throw new TradeException('银行卡单日提现不可超过1次');
            }
            $limit = 5000;
            if (in_array($member->phone, [
                '18935186119',
                '15333681459',
                '18643195015',
                '13754875977',
                '18050129549',
                '13452066116',
                '13788526976',
                '17377109103',
                '18076485252',
                '13377180005',
                '17776258353',
                '13481093989',
                '18878538859',
                '15910116376'
            ])){
                $limit = 10000;
            }
            $todayAmount = BankDraw::todayAmount($member->id);
            if ($todayAmount + $amount > $limit){
                throw new TradeException('单日提现额度为' . $limit);
            }
            if (BankDraw::todayCardAmount($bankAccount->card) + $amount > $limit){
                throw new TradeException('单日提现额度为' . $limit);
            }
            // 单日总额度
            if (Member::todayDrawAmount($member->id)  + $amount > 5000) {
                throw new TradeException('单日提现总额度为5000');
            }
            // redis 检查单日额度
            if (AlipayDraw::todayMemberAmount($member->id) + $amount > $limit) {
                throw new TradeException('单日提现额度为' . $limit);
            }
            $order_no = date('Ymdh') . rand(100000, 999999);
            Log::debug('order_no: ' . $order_no);

            $bankdraw = [
                'member_id' => $member->id,
                'order_no' => $order_no,
                'amount' => $amount,
                'bank' => $bankAccount->bank,
                'card' => $bankAccount->card,
                'name' => $bankAccount->name,
                'headbank' => $bankAccount->headbank,
            ];

            $accountId = $accountService->getAccountId($member->id);
            $accountService->addAsset($accountId , Account::BALANCE, '-' . $amount, '');
            $r = FinanceService::record($member->id, Account::BALANCE, Finance::WALLET_BANKCARD_DARW,
                -1 * $amount, 0, '提现到银行卡,金额:'.$amount.'元', $order_no);
            if(!$r) {
                throw new \Exception('提现失败');
            }
            BankDraw::create($bankdraw);
            AlipayDraw::todayMemberIncrby($member->id, intval($amount));
            DB::commit();
            return ApiResUtil::ok('提现完成，预计24小时到账！');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (!$e instanceof TradeException){
                \Log::error($_REQUEST);
                \Log::error($e->getTraceAsString());
                $msg = '提交失败，请稍候再试';
            }
            DB::rollBack();
            return ApiResUtil::error($msg);
        }

    }

}