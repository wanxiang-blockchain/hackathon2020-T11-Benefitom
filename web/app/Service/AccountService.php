<?php
namespace App\Service;

use App\Exceptions\TradeException;
use App\Model\Account;
use App\Model\AccountFlow;
use App\Model\AssetType;
use App\Model\CostLog;
use App\Model\Score;
use App\Model\ScoreLog;
use App\Model\TradeLog;
use App\Model\TradeSet;
use Illuminate\Support\Facades\Storage;
use App\Repository\UserRepository;
use App\Model\Asset;
use App\Model\Finance;
use App\Exceptions\NotEnough;
use DB;
use Carbon\Carbon;
use Mockery\Exception;

class AccountService
{
	const REMEMBER_TRADE_TOKEN_KEY = 'Lasdnf*u13!';

    public function getAccountId($member_id) {
        return static::fetchAccountId($member_id);
    }

	public static function fetchAccountId($member_id) {
		$account =  Account::where("member_id", $member_id)->first();
		if (!$account) {
            throw new TradeException('error: fetch account id failed, wrong member id: ' . $member_id);
		}
        return $account->id;
	}

    public function addAsset($account_id, $asset_type, $amount, $unlock_time = null) {
        DB::beginTransaction();

        Asset::where('account_id', $account_id)
            ->where('asset_type', $asset_type)
            ->lockForUpdate()
            ->get();
        if ($unlock_time) {
            $asset = new Asset();
            $asset->fill([
                'account_id' => $account_id,
                'asset_type' => $asset_type,
                'amount'     => $amount
            ]);
            $asset->is_lock = 1;
            $asset->unlock_time = $unlock_time;
            $asset->save();
        } else {
            $asset = Asset::where('asset_type', $asset_type)
                ->where('account_id', $account_id)
                ->where('is_lock', 0)
                ->first();
            if ($asset) {
                $asset->amount += $amount;
                $asset->save();
            } else {
                $asset = Asset::create([
                    'account_id' => $account_id,
                    'asset_type' => $asset_type,
                    'amount'     => $amount
                ]);
                if (!$asset) DB::rollback();
            }
        }
        DB::commit();
    }
    //转账
    public function transferTo($from_id, $to_id, $asset_type, $amount, $lock_time = null)
    {
        DB::beginTransaction();
        $from_amount = $this->assetAmount($from_id, $asset_type);
        $this->mergeAsset($from_id, $asset_type);
        if ($from_amount < $amount) {
            DB::rollback();
            throw new NotEnough('asset_not_enough');
        }

        $from_account_id = $this->getAccountId($from_id);
        $to_account_id   = $this->getAccountId($to_id);
        if ($from_account_id == null || $to_account_id == null){
            DB::rollback();
        }

        $this->addAsset($from_account_id,$asset_type, -1 * $amount,$lock_time);
        $this->addAsset($to_account_id,$asset_type ,$amount, $lock_time);
        DB::commit();

    }

    //充值
    public function recharge($account_id, $amount, $asset_type = Account::BALANCE) {
        $this->addAsset($account_id, $asset_type, $amount, null);
    }

    //合并资产
    public function mergeAsset($member_id, $asset_type)
    {
        DB::beginTransaction();
        $account = Account::where('member_id', $member_id)->first();
        if ($account == null) {
            DB::rollback();

            return;
        }
        $account_id = $account->id;
        // select count(`amount`) as aggregate from `assets` where `asset_type` = ? and `is_lock` = ? and `account_id` = ? and `assets`.`deleted_at` is null
        $asset_number = Asset::where('asset_type', $asset_type)
            ->where('is_lock', 0)
            ->where('account_id',$account->id)
            ->count('amount');
        if ($asset_number == 1) {
            DB::commit();  //当资产项只有一项的时候不用合并
            return;
        }

        $amount = $this->assetAmount($member_id, $asset_type);

        // 获取原有成本
	    $oriAsset = Asset::fetchAssetData($account->id, $asset_type);

        // ?? 删除原对该资产的持有，这里如果是自己同自己的交易，会把原来带有order_id的也删除后面就取不出来，成本也将丢失
         Asset::where('asset_type', $asset_type)
            ->where('is_lock', 0)
            ->where('account_id',$account->id)
            ->forcedelete();

        // 新建现在该资产持有?
        Asset::create([
            'account_id' => $account_id,
            'asset_type' => $asset_type,
            'amount'     => $amount,
	        'cost' => $oriAsset['cost']
        ]);
        DB::commit();
    }

    public function mergeBalance($member_id)
    {
        return $this->mergeAsset($member_id, Account::BALANCE);
    }

    //可用余额
    public function balance($member_id)
    {
        return $this->assetAmount($member_id, Account::BALANCE);
    }

    public function isBalanceEnough($member_id, $amount)
    {
        $balance = $this->balance($member_id);
        return $balance >= $amount;
    }
    //冻结资产数量
    public function freeze_asset($member_id, $asset_type, $ope='=')
    {
        DB::beginTransaction();
        $account_id = $this->getAccountId($member_id);
        // 将解冻资产操作统一放到零点处理
//        DB::table('assets')
//            ->where('asset_type', $ope,  $asset_type)
//            ->where('is_lock', 1)
//            ->whereRaw('unlock_time < now()')
//            ->update(['is_lock' => 0]);
        if ($account_id == null) {
            DB::rollback();
            return 0;
        }
        $amount = Asset::where('asset_type', $ope, $asset_type)
            ->where('account_id', $account_id)
            ->where('is_lock', 1)
            ->sum('amount');
        DB::commit();
        return $amount;
    }
    //可用资产数量，TODO 此处会产生死锁，没必要启事务，第一条语句修改is_lock不影响交易，二一条读数量不影响其他原子操作
    public function assetAmount($member_id, $asset_type)
    {
	    $account_id = $this->getAccountId($member_id);
	    if ($account_id == null) {
		    DB::rollback();
		    return 0;
	    }
//        DB::beginTransaction();
        // 把unlock_time小于当前时间的所有is_lock的资产改为is_lock=0
	    // update `assets` set `is_lock` = ? where `account_id` = ? `asset_type` = ? and `is_lock` = ? and unlock_time < now()
	    // 这条语句没有必要启事务啊。
	    // TODO 将资产解冻，统一放到零点处理
//        DB::table('assets')
//	        ->where('account_id', $account_id)  // 加入 account_id 试图解决死锁问题
//            ->where('asset_type',  $asset_type)
//            ->where('is_lock', 1)
//            ->whereRaw('unlock_time < now()')
//            ->update(['is_lock' => 0]);
        // select sum(`amount`) as aggregate from `assets` where `asset_type` = ? and `account_id` = ? and `is_lock` = ? and `assets`.`deleted_at` is null
        $amount = Asset::where('asset_type', $asset_type)
            ->where('account_id', $account_id)
            ->where('is_lock', 0)
            ->sum('amount');
//        DB::commit();

        return $amount;
    }


	/**
	 *  按t+n可用资产数量
	 * 1、取出n天内卖出数量减去资产总数取绝对值
	 * @desc tPlusAssetAmount
	 * @param $member_id
	 * @param $asset_type
	 * @return int
	 */
    public function tPlusAssetAmount($member_id, $asset_type, $t_plus=null)
    {
    	if (is_null($t_plus)) {
		    $tradeSet = TradeSet::where('asset_type', $asset_type)->first();
		    $t_plus = isset($tradeSet['t_plus'])  ? $tradeSet['t_plus'] : 0;
	    }
    	$total_amount = $this->assetAmount($member_id, $asset_type);
		// 取出t_plus天内买入数量
	    $time =  date('Y-m-d', strtotime("-$t_plus days")) . ' 23:59:59';
	    $traded_amount = TradeLog::where('asset_type', $asset_type)
		    ->where('type', 1)
		    ->where('buyer_id', $member_id)
		    ->where('created_at', '>', $time)
	        ->sum('amount');
	    return $traded_amount > $total_amount ? 0 : $total_amount - $traded_amount;
    }


    public function notBalanceAmount($member_id)
    {
        DB::beginTransaction();
        $account_id = $this->getAccountId($member_id);
        if ($account_id == null) {
            DB::rollback();
            return 0;
        }
        // 将解冻资产操作统一放到零点处理
//        DB::table('assets')
//            ->where('asset_type',  '!=', Account::BALANCE)
//            ->where('is_lock', 1)
//            ->whereRaw('unlock_time < now()')
//            ->update(['is_lock' => 0]);

        $amount = Asset::where('asset_type', '!=', Account::BALANCE)
            ->where('account_id', $account_id)
            //->where('is_lock', 0)
            ->sum('amount');
        DB::commit();

        return $amount;
    }

    //总资产
    public function totalAmount($member_id)
    {
        $account_id = $this->getAccountId($member_id);
        $result = \DB::table('assets')
            ->leftJoin('asset_types', 'asset_type', 'code')
            ->where('account_id', $account_id)
            ->sum(\DB::raw("asset_types.market_value * assets.amount"));
        return $result;
    }

    /**
     *   冻结资产的交易
     *   $asset_type 为资产编号
     *   $amount   冻结交易数量
     *   $order_id    交易id
     *   $lock      1 为冻结 0为交易冻结的数量
     */
    public function addFreeze($account_id,$asset_type,$amount,$lock,$order_id) {
        if($lock == 1){
            DB::beginTransaction();
            $asset = new Asset();
            $asset->fill([
                'account_id' => $account_id,
                'asset_type' => $asset_type,
                'amount'     => $amount,
                'order_id'   => $order_id
            ]);
            $asset->is_lock = 1;
            $asset->unlock_time = Asset::fetchUnlockTime();
            $asset->save();
            $this->addAsset($account_id,$asset_type, -1 * $amount);
            DB::commit();
        }else if ($lock == 0){
            DB::beginTransaction();
            $asset = Asset::where('order_id',$order_id)->first();
            if(!$asset){
                DB::rollBack();
                throw new Exception('资产冻结失败');
            }
            $asset->amount += $amount;
            if($asset->amount == 0){
                $asset->forcedelete();
            }else{
                $asset->save();

            }
            DB::commit();
        }

    }
    //交易转账 TODO 买家新进资产要按T+n进行冻结
    public function transfer($from_id, $to_id, $asset_type, $amount,$order_id)
    {
        DB::beginTransaction();
        $from_amount = Asset::where('order_id',$order_id)->first()->amount;
        $this->mergeAsset($from_id, $asset_type);
        if ($from_amount < $amount) {
            DB::rollback();
            throw new NotEnough('资产不足');
        }
        $from_account_id = $this->getAccountId($from_id);
        $to_account_id   = $this->getAccountId($to_id);
        if ($from_account_id == null || $to_account_id == null){
            DB::rollback();
        }
        // 减少或删除卖家所持相关资产
        $this->addFreeze($from_account_id,$asset_type, -1 * $amount,0,$order_id);
        $this->addAsset($to_account_id,$asset_type ,$amount);
        DB::commit();

    }

    //撤销挂单
    public function revoked($order_id){
            DB::beginTransaction();
            $asset = Asset::where('order_id',$order_id)->first();
            if (!$asset) {
            	DB::rollBack();
            	return false;
            }
            $asset -> is_lock = 0;
            $asset->save();
            $amount = $asset->amount;
            $member_id = Account::find($asset->account_id)->member_id;
            if($asset->asset_type == Account::BALANCE){
	            $this->mergeAsset($member_id,$asset->asset_type);
                FinanceService::record($member_id, $asset->asset_type, 6, $amount, 0, '撤销挂单解冻金额:'.$amount.'元');
            }else{
            	// TODO 撤单时成本被置为NULL了
                $asset_name = AssetType::where('code',$asset->asset_type)->first()->name;
	            $this->mergeAsset($member_id,$asset->asset_type);
	            FinanceService::record($member_id, $asset->asset_type, 6, 0, $amount, '撤销挂单解冻'.$asset_name.$amount.'个');
            }
            DB::commit();
    }

    public function fetchPoundage($price, $amount, $rate)
    {
	    return round($price * $amount * $rate,2);
    }

    //扣除手续费
    public function fee($member_id,$asset_type,$price){
        $account_id = $this->getAccountId($member_id);
        $realPrice = round(-1 * $price,2);
        $this->addAsset($account_id,$asset_type ,$realPrice);
         FinanceService::record($member_id, $asset_type, 5, $realPrice, 0, '交易手续费扣除:'.$realPrice.'元');
    }

	/**
	 * 买入后成本，计算
	 * @desc buyCostCount
	 * @param $hold_price
	 * @param $hold_amount
	 * @param $buy_price
	 * @param $buy_amount
	 * @return double
	 */
    public function buyCost($hold_price, $hold_amount, $buy_price, $buy_amount, $account_id, $asset_type)
    {
    	// 计算成本，小数点后第5位四舍五入
		$cost = round(($hold_price * $hold_amount + $buy_price * $buy_amount) / ($hold_amount + $buy_amount), 2);
		DB::beginTransaction();
		// 所有修改成本
	    DB::table('assets')->where('account_id', $account_id)
		    ->where('asset_type', $asset_type)
		    ->update(['cost' => $cost]);
	    CostLog::record($account_id, ($hold_amount + $buy_amount), $asset_type, $cost, "买入{$buy_amount}，计算成本为：{$cost}");
	    DB::commit();
    }

	/**
	 * 卖出后成本，积分计算
	 * @desc saleConstCount
	 * @param $hold_price
	 * @param $hold_amount
	 * @param $buy_price
	 * @param $buy_amount
	 * @param $account_id
	 * @param $poundage  交易手续费
	 */
    public function saleCost($hold_price, $hold_amount, $sale_price, $sale_amount, $account_id, $asset_type, $member_id, $poundage)
    {
		$profit = $this->profit($hold_price, $sale_price, $sale_amount, $poundage);
	    DB::beginTransaction();
	    $asset = Asset::getUnlockAsset($account_id, $asset_type);
	    $score = 0;
	    // 如果盈利，添加积分，盈利就不重新核算成本
	    if ($profit > 0){
	        $score = round($profit * 0.2, 2);
		    $scoreModel = Score::where('account_id', $account_id)->first();
		    if($scoreModel) {
		    	$scoreModel->score += $score;
		    	$scoreModel->save();
		    } else {
		    	Score::create([
					'score' => $score,
				    'account_id' => $account_id,
			    ]);
		    }

		    ScoreLog::create([
		        'score' => $score,
			    'account_id' => $account_id,
			    'order_id' => $asset->order_id,
			    'type' => ScoreLog::TYPE_TRADE_PROFIT
		    ]);

		    // 扣除账户余额与积分等额数
		    $this->addAsset($account_id, Account::BALANCE, -1 * $score);
		    FinanceService::record($member_id, $asset_type, Finance::SCORE, -1 * $score, 0, '利润兑换为积分: -'. $score .'元');

		    // 如果盈利，扣了积分就不变更成本，避免重复扣除积分
	    }
	    DB::commit();

	    // 利润扣除积分后，计算成本，how about hold_amount == sale_amount ?
	    // 卖出统一不再变更成本 2017-08-17
//	    if($hold_amount == $sale_amount)
//	    	$cost = 0;
//	    else
//	        $cost = $hold_price - round( ($profit - $score )/ ($hold_amount - $sale_amount), 2);
//
//	    // 所有修改成本
//	    DB::table('assets')->where('account_id', $account_id)
//		    ->where('asset_type', $asset_type)
//	        ->update(['cost' => $cost]);
//
//	    CostLog::record($account_id, ($hold_amount - $sale_amount), $asset_type, $cost, "卖出{$sale_amount}，计算成本为：{$cost}");

    }

	/**
	 * 积分计算
	 * @desc score
	 * @param $hold_price
	 * @param $sale_price
	 * @param $sale_amount
	 * @return mixed
	 */
    public function score($hold_price, $sale_price, $sale_amount)
    {
	    $profit = $this->profit($hold_price, $sale_price, $sale_amount);
	    return $profit * 0.2;
    }

	/**
	 * 计算利润
	 * @desc profit
	 * @param $hold_price
	 * @param $hold_amount
	 * @param $sale_price
	 * @param $sale_amount
	 * @return mixed
	 */
    public function profit($hold_price, $sale_price, $sale_amount, $poundage)
    {
		return ($sale_price - $hold_price) * $sale_amount - $poundage;
    }

    public function fetchScore($account_id)
    {
    	$score = Score::where('account_id', $account_id)->first();
    	return $score ? $score->score : 0;
    }

    public function createRemeberToken($id)
    {
		$tk = time() . '|' . md5(self::REMEMBER_TRADE_TOKEN_KEY . microtime());
		DB::statement('update accounts set remember_token = ? where id = ?', [$tk, $id]);
		return $tk;
    }

	/**
	 * 检查交易密码是否正确或是否过期
	 * @desc checkRemberToken
	 * @param $id
	 * @param $token
	 * @return bool
	 */
    public function checkRemberToken($id, $token)
    {
    	if (empty($token)) {
    		return false;
	    }
	    if (!Account::where('id', $id)->where('remember_token', $token)->exists()) {
    	    return false;
	    }
		$arr = explode('|', $token);
    	if (time() - $arr[0] > 1800) {
    		return false;
	    }
	    return true;
    }

}