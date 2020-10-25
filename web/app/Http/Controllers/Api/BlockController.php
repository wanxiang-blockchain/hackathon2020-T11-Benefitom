<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2018-12-31
 * Time: 12:29
 */

namespace App\Http\Controllers\Api;


use App\Console\Commands\BtshopLockJob;
use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Account;
use App\Model\Artbc;
use App\Model\Artbc\BtConfig;
use App\Model\Artbc\BtScoreUnlock;
use App\Model\BlockSale;
use App\Model\BlockTibi;
use App\Model\Btshop\Bankcard;
use App\Model\Btshop\BlockAsset;
use App\Model\Btshop\BlockAssetLog;
use App\Model\Btshop\BlockAssetType;
use App\Model\Btshop\BlockRechargeLog;
use App\Model\Btshop\BlockTiqu;
use App\Model\BlockTransferLog;
use App\Model\Btshop\Btaccount;
use App\Model\Btshop\BtshopOrder;
use App\Model\Btshop\BtshopProduct;
use App\Model\Finance;
use App\Model\ListModel;
use App\Model\Member;
use App\Model\Profile;
use App\Service\AccountService;
use App\Service\FinanceService;
use App\Service\SsoService;
use App\Service\ValidatorService;
use App\Utils\ApiResUtil;
use App\Utils\BtshopUtil;
use App\Utils\PushUtil;
use App\Utils\RedisKeys;
use App\Utils\RedisUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Twilio\Rest\Api;

class BlockController extends Controller
{

    public function logs(Request $request)
    {
        $member = Member::apiCurrent();
        $code = $request->get('code', '300002');
        $query = BlockAssetLog::where('member_id', $member->id)
            ->where('code', $code);
        $listModel = new ListModel($query);
        $models = $listModel->fetchModels([
            'id', 'amount', 'balance', 'created_at', 'type'
        ]);
        $count = count($models);
        $list = [];
        foreach($models as $i => $model){
            $list[$i] = $model->toArray();
            $list[$i]['typeLabel'] = BlockAssetLog::fetchTypeLable($model->type);
        }
        return ApiResUtil::ok([
            'hasMore' => intval($count == ApiResUtil::PAGENATION),
            'list' => $list
        ]);
    }

    /**
     * account | string  | 1 | 充值地址，以太（0x...）cca(12位)
    amount | float | 1 | 充值数量
    code | string | 1 | 充值渠道 1 ArTBC 2 ARTTBC
     */
    public function prerecharge(Request $request)
    {
        $account = $request->get('account');
        $amount = $request->get('amount');
        $code = $request->get('code');
        if (empty($account) || !is_string($account)) {
            return ApiResUtil::error('地址为空或格式不对');
        }
        if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
            return ApiResUtil::error('充值数量不正确');
        }
        if (empty($code) || !BlockAssetType::valideCode($code)) {
            return ApiResUtil::error('充值类型不存在');
        }
        $member = Member::apiCurrent();
        // 提现频次限制，一分钟只允许一次
        $key = RedisKeys::API_PER_TIMES_LIMIT_PRE . 'block:prerecharge:' . $member->id;
        if (RedisUtil::get($key) == 1){
            return ApiResUtil::error('操作过于频繁，请稍候再试');
        }
        RedisUtil::set($key,1, 60);
        if ($code == '300001'){
//            return ApiResUtil::error('ArTBC充值暂停服务');
        }
        $orderNum = date('YmdH') . randStr(8, 'NUMBER');
        if (!BlockRechargeLog::add($member->id, $code, $account, $amount, $orderNum)){
            return ApiResUtil::error('订单创建失败');
        }
        return ApiResUtil::ok([
            'order_num' => $orderNum
        ]);

    }

    /**
     *     tx  | string  |  1 | tx hash
    order_num | string | 1 | 充值单号
     */
    public function recharge(Request $request)
    {
        $tx = $request->get('tx');
        $order_num = $request->get('order_num');
        $txdata = $request->get('txdata');
        if (empty($tx) || empty($order_num) || empty($txdata)) {
            return ApiResUtil::error(ApiResUtil::WRONG_PARAMS);
        }
        \DB::beginTransaction();
        try{
            $model = BlockRechargeLog::fetchByOrderNum($order_num);
            if (empty($model)) {
                throw new TradeException('订单不存在');
            }
            if ($model->stat !== BlockRechargeLog::STAT_INIT) {
                throw new TradeException('订单已处理');
            }
            if (BlockRechargeLog::fetchByTxCode($tx, $model->code)) {
                Log::info('txdata', ['txdata: ' => $txdata]);
                throw new TradeException('该区块已被绑定');
            }

            $model->tx = $tx;
            $model->stat = BlockRechargeLog::STAT_ING;
            $model->txdata = $txdata;
            if (!$model->save()) {
                throw new TradeException('服务器异常');
            }
            // console confirm tx finish to write block_asseet
//            BlockAssetLog::record($model->member_id, $model->code, $model->amount, BlockAssetLog::TYPE_RECHARGE, '充值' . $model->amount);
            Redis::lpush(BtshopUtil::BLOCK_RECHARGE_TX_KEY, $model->order_num);
            DB::commit();
            return ApiResUtil::ok();
        }catch (\Exception $e){
            DB::rollBack();
            \Log::error(($e->getMessage()));
            return ApiResUtil::error($e->getMessage());
        }
    }

    /**
    product_id |  int  | 1   | 商品id
    amount | int | 1 | 购买数量
    paytype | int | 1 | 0 ARTTBC(cca)  1 ArTBC(eth + rmb) 2 rmb
     */
    public function pay(Request $request)
    {
//        return ApiResUtil::error('系统升级，暂停服务');
        /**
         * 1. 验证产品
         * 2. 验证购买数量限制
         * 3. 验证余额充足
         * 4. write btsohp_orders
         * 5. write invite parents
         */
        $product_id = $request->get('product_id');
        $amount = intval($request->get('amount'));
//        $paytype = $request->get('paytype');
        if (empty($product_id) || !is_numeric($product_id)){
            return ApiResUtil::error('产品不存在');
        }
        if (empty($amount) || !is_integer($amount) || $amount < 0) {
            return ApiResUtil::error('数量不正确');
        }
//        if (!isset($paytype) || !BtshopProduct::validePaytype($paytype)){
//            return ApiResUtil::error('支付方式不正确');
//        }

        $member = Member::apiCurrent();

        // 提现频次限制，一分钟只允许一次
        $key = RedisKeys::API_PER_TIMES_LIMIT_PRE . 'block:pay:' . $member->id;
        if (RedisUtil::get($key) == 1){
            return ApiResUtil::error('操作过于频繁，请稍候再试');
        }
        RedisUtil::set($key,1, 60);

        if (BlockAsset::codeBalance($member->id, '300003') >= 100000){
            return ApiResUtil::error('该账号不能下单');
        }
        DB::beginTransaction();
        try{
            $product = BtshopProduct::fetchEnabelModel($product_id);
            if (empty($product)){
                throw new TradeException('产品不存在');
            }
            $paytype = $product->paytype;
            // 判断当天购买数量
            $todayBoughtSum = BtshopOrder::todayBoughtCount($member->id, $product->id);
            if ($amount + $todayBoughtSum > $product->per_limit){
                throw new TradeException('每天购买数量不得大于' . $product->per_limit);
            }
            // 判断当天购买积分
            $todayUnlockScore = BtScoreUnlock::todayUnlockScore($member->id);
            if ($todayUnlockScore + ($product->score * $amount) > 22000) {
                throw new TradeException('每天锁仓积分不得大于22000');
            }
            $accountService = new AccountService();
            $account_id = $accountService->getAccountId($member->id);
            switch ($paytype){
                case BtshopProduct::PAYTYPE_BT:
                    $asset = BlockAsset::fetchModel($member->id, BlockAssetType::CODE_ARTTBC);
                    if (empty($asset)){
                        throw new TradeException('ARTTBC余额不足，请前往充值');
                    }
                    if ($product->bt_price * $amount > $asset->balance){
                        throw new TradeException('ARTTBC余额不足，请前往充值');
                    }

                    BlockAssetLog::record($member->id, BlockAssetType::CODE_ARTTBC, -1 * $product->bt_price * $amount,
                        BlockAssetLog::TYPE_CONSUME, '艺行派购买《' . $product->name . '》, 花费 ARTTBC ' . $amount * $product->bt_price);
                    break;
                case BtshopProduct::PAYTYPE_ARTBC:
                    $asset = BlockAsset::fetchModel($member->id, BlockAssetType::CODE_ARTBC);
                    if (empty($asset) || $product->price * $amount > $asset->balance){
                        throw new TradeException('ARTBC余额不足，请前往充值');
                    }
                    BlockAssetLog::record($member->id, BlockAssetType::CODE_ARTBC, -1 * $product->price * $amount,
                        BlockAssetLog::TYPE_CONSUME, '艺行派购买《' . $product->name . '》, 花费 ARTBC ' . $amount * $product->price);
                    // todo 一级奖励
                    $parent = Member::find($member->wallet_invite_member_id);
                    if ($parent) {
                        BlockAssetLog::record($parent->id, BlockAssetType::CODE_ARTBC, 0.1 * $product->price * $amount,
                            BlockAssetLog::TYPE_P_PRIZE, '下级 购买《' . $product->name . '》, 赠送 ARTBC ' . 0.1 * $amount * $product->price);
                        $pParent = Member::find($parent->wallet_invite_member_id);
                        if ($pParent){
                            BlockAssetLog::record($pParent->id, BlockAssetType::CODE_ARTBC, 0.05 * $product->price * $amount,
                                BlockAssetLog::TYPE_PP_PRIZE, '下下级 购买《' . $product->name . '》, 赠送 ARTBC ' . 0.05 * $amount * $product->price);
                        }
                    }
                    break;
                case BtshopProduct::PAYTYPE_RMN:
                    if (!$accountService->isBalanceEnough($member->id, $product->rmb_price * $amount)){
                        throw new TradeException('现金余额不足，请前往充值');
                    }
                    $accountService->addAsset($account_id, Account::BALANCE, -1 * $product->rmb_price * $amount, '');
                    if(!FinanceService::record($member->id, Account::BALANCE, Finance::WALLET_SALE_COST, -1 * $product->rmb_price * $amount, 0,
                         '艺行派购买商品《'. $product->name . '》,金额:'.$product->rmb_price * $amount.'元')) {
                        throw new TradeException('扣款失败');
                    }
                    break;
                case BtshopProduct::PAYTYPE_ARTBCS:
                    $asset = BlockAsset::fetchModel($member->id, BlockAssetType::CODE_ARTBCS);
                    if (empty($asset) || $product->artbcs_price* $amount > $asset->balance){
                        throw new TradeException('ARTBCS余额不足，请前往充值');
                    }
                    if (!$accountService->isBalanceEnough($member->id, $product->rmb_price * $amount)){
                        throw new TradeException('现金余额不足，请前往充值');
                    }
                    BlockAssetLog::record($member->id, BlockAssetType::CODE_ARTBCS, -1 * $product->artbcs_price * $amount,
                        BlockAssetLog::TYPE_CONSUME, '艺行派购买《' . $product->name . '》, 花费 ARTBCS ' . $amount * $product->artbcs_price);
                    $accountService->addAsset($account_id, Account::BALANCE, -1 * $product->rmb_price * $amount, '');
                    if(!FinanceService::record($member->id, Account::BALANCE, Finance::WALLET_SALE_COST,-1 * $product->rmb_price * $amount,
                        0, '艺行派购买商品,金额:'.$product->rmb_price * $amount.'元')) {
                        throw new TradeException('扣款失败');
                    }
                    break;
                case BtshopProduct::PAYTYPE_ARTTBC_ARTBC:
                    $arttbc_asset = BlockAsset::fetchModel($member->id, BlockAssetType::CODE_ARTTBC);
                    $artbc_asset = BlockAsset::fetchModel($member->id, BlockAssetType::CODE_ARTBC);
                    if (empty($arttbc_asset) || empty($artbc_asset)){
                        throw new TradeException('资产库存不足，请前往充值');
                    }
                    if ($product->bt_price * $amount > $arttbc_asset->balance){
                        throw new TradeException('ARTTBC余额不足，请前往充值');
                    }
                    if ($product->price * $amount > $artbc_asset->balance){
                        throw new TradeException('ArTBC余额不足，请前往充值');
                    }

                    BlockAssetLog::record($member->id, BlockAssetType::CODE_ARTTBC, -1 * $product->bt_price * $amount,
                        BlockAssetLog::TYPE_CONSUME, '艺行派购买《' . $product->name . '》, 花费 ARTTBC ' . $amount * $product->bt_price);
                    BlockAssetLog::record($member->id, BlockAssetType::CODE_ARTBC, -1 * $product->price * $amount,
                        BlockAssetLog::TYPE_CONSUME, '艺行派购买《' . $product->name . '》, 花费 ArTBC ' . $amount * $product->price);
                    break;
            }
            $orderNum = date('YmdH') . randStr(8, 'NUMBER');
            BtshopOrder::add($orderNum, $product_id, $product->price, $amount, $product->score, $paytype, '', $member->id, BtshopOrder::STAT_DONE);
//            BtScoreUnlock::inviteAdd($member->id, $product->score * $amount, $orderNum);
            DB::commit();
            return ApiResUtil::ok([
                'order_num' => $orderNum
            ]);
        }catch (\Exception $e){
            DB::rollBack();
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());
            return ApiResUtil::error($e->getMessage());
        }
    }

    public function ti(Request $request)
    {
//        return ApiResUtil::error('该功能暂不可用');
        $h = date('H');
        if ($h < 10 || $h >= 17){
            return ApiResUtil::error('提取时间为每天10-17时！');
        }
        $code = $request->get('code');
        $code = '300002';
        $amount = intval($request->get('amount'));
        if (empty($amount) || !is_integer($amount) || $amount < 100 || $amount > 1500) {
            return ApiResUtil::error('提取数量应在100-1500之间');
        }

        if ($amount % 100 != 0){
            return ApiResUtil::error('提取数量应为100的整数倍');
        }

        if (empty($code) || !BlockAssetType::valideCode($code)) {
            return ApiResUtil::error('提取资产类型不存在');
        }
        $member = Member::apiCurrent();
        $btaccount = Btaccount::fetchModel($member->id);
        if (empty($btaccount)){
            return ApiResUtil::error('版通账户尚未绑定');
        }

        // 提现频次限制，一分钟只允许一次
        $key = RedisKeys::API_PER_TIMES_LIMIT_PRE . 'arttbc:ti:' . $member->id;
        if (RedisUtil::get($key) == 1){
            return ApiResUtil::error('提现过于频繁，请稍候再试');
        }
        RedisUtil::set($key,1, 60);

        DB::beginTransaction();
        try{
            $asset = BlockAsset::fetchModel($member->id, $code);
            if (empty($asset) || $asset->balance < $amount){
                throw new TradeException('库存不足，请前往充值');
            }
            // 单用户一天限制1500
            if (BlockTiqu::memberTodayAmount($member->id) + $amount > 1500){
                throw new TradeException('单用户单日提取上限1500');
            }

            // 单日总额度
            if (Member::todayDrawAmount($member->id)  + $amount * BtConfig::getPrice() > 5000) {
                throw new TradeException('单日提现总额度为5000');
            }

            // 单账户一天限制1500
            if (BlockTiqu::accountTodayAmount($btaccount->account) + $amount > 1500){
                throw new TradeException('单账户单日提取上限1500');
            }

            BlockTiqu::add($member->id, $btaccount->account, $amount, $code, $btaccount->name);
            BlockAssetLog::record($member->id, $code, -1 * $amount,
                BlockAssetLog::TYPE_TI_BT, '提取为版通 ' . $amount);
            DB::commit();
            return ApiResUtil::ok();
        }catch (TradeException $e){
            DB::rollBack();
            return ApiResUtil::error($e->getMessage());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return ApiResUtil::error('提取失败');
        }
    }

    public function saleARTTBC(Request $request, ValidatorService $validatorService)
    {
        $amount = intval($request->get('amount'));
        if (empty($amount) || !is_integer($amount) || $amount < 1) {
            return ApiResUtil::error('数量不正确');
        }
        $code = BlockAssetType::CODE_ARTTBC;

        $member = Member::apiCurrent();
        DB::beginTransaction();
        try{
            $asset = BlockAsset::fetchModel($member->id, $code);
            if (empty($asset) || $asset->balance < $amount){
                throw new TradeException('余额不足，请前往充值');
            }

            $order_no = date('YmdHis') . randStr(8);
            BlockAssetLog::record($member->id, $code, -1 * $amount,
                BlockAssetLog::SALE_ARTTBC, '售出' . $amount, $order_no);
            $bankcard = Bankcard::fetchModel($member->id);
            BlockSale::add($member->id, $amount, $bankcard->name, $bankcard->card, $bankcard->headbank, $order_no);
            DB::commit();
            return ApiResUtil::ok();
        }catch (TradeException $e){
            DB::rollBack();
            return ApiResUtil::error($e->getMessage());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return ApiResUtil::error('售出');
        }
    }

    public function toRmb(Request $request, AccountService $accountService)
    {
//        return ApiResUtil::error('该功能已下线');
        $type = $request->get('type', 1);
        $code = BlockAssetType::CODE_ARTBCS;
        $amount = intval($request->get('amount'));
        if (empty($amount) || !is_integer($amount)) {
            return ApiResUtil::error('数量不正确');
        }

        if (empty($code) || !BlockAssetType::valideCode($code)) {
            return ApiResUtil::error('提取资产类型不存在');
        }

        $member = Member::apiCurrent();

        // 提现频次限制，一分钟只允许一次
        $key = RedisKeys::API_PER_TIMES_LIMIT_PRE . 'arttbc:tormb:' . $member->id;
        if (RedisUtil::get($key) == 1){
            return ApiResUtil::error('提现过于频繁，请稍候再试');
        }
        RedisUtil::set($key,1, 60);

        DB::beginTransaction();
        try{
            $asset = BlockAsset::fetchModel($member->id, $code);
            $canCash = BlockAsset::artbcsCanCash($member->id);
            if (empty($asset) || $canCash < $amount || $asset->balance < $amount){
                throw new TradeException('可兑现库存不足，请前往充值');
            }


            // type == 1 rmb + arttbc, type = 2 arttbc
            if ($type == 1){
                $config = BtConfig::fetchOne();
                // 50% to tmb
                $toarttbc = intval($amount / 2);
                $tormb = $amount - $toarttbc;
                // 扣除 artbcs
                BlockAssetLog::record($member->id, $code, -1 * $amount,
                    BlockAssetLog::TYPE_TO_RMB_ARTTBC, '提取为现金+ARTTBC' . $amount);
                // 50% to rmb
                $account_id = $accountService->getAccountId($member->id);
                $accountService->addAsset($account_id, Account::BALANCE, $config->price * $tormb, '');
                if(!FinanceService::record($member->id, Account::BALANCE, Finance::WALLET_ARTTBC_EXCHANGE ,$config->price * $tormb,
                    0, 'ARTBCS 兑现 ' . $config->price * $tormb.'元')) {
                    throw new TradeException('扣款失败');
                }

                // 50% to arttbc
                BlockAssetLog::record($member->id, BlockAssetType::CODE_ARTTBC, $toarttbc,
                    BlockAssetLog::TYPE_FROM_ARTBCS, '兑换自ARTBCS' . $toarttbc);
            }else{
                // 扣除 artbcs
                BlockAssetLog::record($member->id, $code, -1 * $amount,
                    BlockAssetLog::TYPE_TO_ARTTBC, '提取为ARTTBC' . $amount);
                BlockAssetLog::record($member->id, BlockAssetType::CODE_ARTTBC, $amount,
                    BlockAssetLog::TYPE_FROM_ARTBCS, '兑换自ARTBCS' . $amount);
            }

            DB::commit();
            return ApiResUtil::ok();
        }catch (TradeException $e){
            DB::rollBack();
            return ApiResUtil::error($e->getMessage());
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return ApiResUtil::error('提取失败');
        }
    }

    public function lastPrice()
    {
        $config = BtConfig::fetchOne();
        return ApiResUtil::ok([
            'price' => $config->price
        ]);
    }

    public function transfer(Request $request)
    {
        $code = $request->get('code');
        $amount = intval($request->get('amount'));
        $receiver = intval($request->get('receiver'));
        $verifycode = $request->get('verifycode');
        if (empty($amount) || !is_integer($amount) || $amount < 0) {
            return ApiResUtil::error('数量不正确');
        }

        if (empty($receiver)) {
            return ApiResUtil::error('转入用户不存在');
        }

        if (empty($verifycode)) {
            return ApiResUtil::error('验证码不得为空');
        }

        if (empty($code) || !BlockAssetType::valideCode($code)) {
            return ApiResUtil::error('转出资产类型不存在');
        }


        $member = Member::apiCurrent();

        $receiver = Member::fetchModelByPhone($receiver);
        if (empty($receiver) || $receiver->locked()) {
           return ApiResUtil::error('转入用户不存在或已被封禁');
        }
        if ($code === '300002'){
//            return ApiResUtil::error('此功能与易兑中心内测相冲突，待易兑中心测试完毕后同时上线。');
            $rand = rand(0, 9);
            if ($rand === 2){
                return ApiResUtil::error('Too many requests, please wait for a while!');
            }
//            $profile = Profile::fetchByMid($member->id);
//            if (empty($profile)) {
//                return ApiResUtil::error('转出功能需要身份认证');
//            }
//            $receiverProfile = Profile::fetchByMid($receiver->id);
//            if (empty($receiverProfile)) {
//                return ApiResUtil::error('转出用户未做身份认证');
//            }
//
//            if ($profile->idno !== $receiverProfile->idno) {
//                return ApiResUtil::error('转出用户和转入用户必须为同一实名');
//            }
        }



        if ($receiver->id == $member->id) {
            return ApiResUtil::error('转出失败:code1');
        }

        // 提现频次限制，一分钟只允许一次
        $key = RedisKeys::API_PER_TIMES_LIMIT_PRE . 'arttbc:transfer:' . $member->id;
        if (RedisUtil::get($key) == 1){
            return ApiResUtil::error('操作过于频繁，请稍候再试');
        }
        RedisUtil::set($key,1, 60);

        $verify = SsoService::smsVerify($member->phone, $member->nationcode, $verifycode, 1);

        if ($verify['code'] !== 0){
            return ApiResUtil::error($verify['data']);
        }

        DB::beginTransaction();
        try{
            $asset = BlockAsset::fetchModel($member->id, $code);
            if (empty($asset) || $asset->balance < $amount){
                throw new TradeException('库存不足');
            }

            $order_no = date('YmdHi').rand(100000, 999999);
            //转出
            BlockAssetLog::record($member->id, $code, -1 * $amount,
                BlockAssetLog::TYPE_TRANSFER, '转出到用户' . $receiver->phone . ':' . $amount, $order_no);
            // 转入
            BlockAssetLog::record($receiver->id, $code,  $amount,
                BlockAssetLog::TYPE_TRANSFER_IN, '用户' . $member->phone . '转入:' . $amount, $order_no);

            $blockTransferLog = new BlockTransferLog();
            // 转出记录
            $blockTransferLog->fill([
                'outer' => $member->id,
                'inner' => $receiver->id,
                'amount' => $amount,
                'code' => $code,
                'order_no' => $order_no
            ]);
            $blockTransferLog->save();
            DB::commit();
//            PushUtil::blockTransferIn($blockTransferLog);
            return ApiResUtil::ok();
        }catch (TradeException $e){
            DB::rollBack();
            return ApiResUtil::error($e->getMessage());
        }catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return ApiResUtil::error('转移失败失败');
        }
    }
    public function tibi(Request $request)
    {
        $amount = intval($request->get('amount'));
        $addr = $request->get('addr');
        $member = Member::apiCurrent();
        $code = BlockAssetType::CODE_ARTBC;
        DB::beginTransaction();
        try{
            $asset = BlockAsset::fetchModel($member->id, $code);
            if (empty($asset) || $asset->balance < $amount){
                throw new TradeException('库存不足');
            }

            $order_no = date('YmdHi').rand(100000, 999999);
            //提币
            BlockAssetLog::record($member->id, $code, -1 * $amount,
                BlockAssetLog::TYPE_TI_BI, '提币到' . $addr . ':' . $amount, $order_no);
            // todo 添加提币记录
            BlockTibi::add($member->id, $order_no, $amount, $addr);
            DB::commit();
            return ApiResUtil::ok();
        }catch (TradeException $e){
            DB::rollBack();
            return ApiResUtil::error($e->getMessage());
        }catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return ApiResUtil::error('提币失败');
        }
    }
}