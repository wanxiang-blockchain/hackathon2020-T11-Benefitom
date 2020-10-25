<?php

namespace App\Model;

use App\Exceptions\TradeException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Asset extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'account_id', 'amount', 'asset_type','order_id','is_lock','unlock_time', 'cost'
    ];

	/**
	 * 获取某用户某资产所有持有情况
	 * @desc fetchAssetData
	 * @param $account_id
	 * @param $code
	 * @return array
	 */
    public static function fetchAssetData($account_id, $code)
    {
    	$assets = static::where('account_id', $account_id)
		    ->where('asset_type', $code)
		    ->get();
    	$cost = 0;
    	$amount = 0;
    	foreach ($assets as $asset) {
            $amount += $asset['amount'];
            !is_null($asset->cost) && $cost = $asset->cost;
	    }
    	return compact('cost', 'amount');
    }

	/**
	 * @desc getUnlockAsset
	 * @param $account_id
	 * @param $code
	 * @return mixed
	 */
    public static function getUnlockAsset($account_id, $code)
    {
    	return static::where('account_id', $account_id)
		    ->where('asset_type', $code)
		    ->where('is_lock', 0)
		    ->first();
    }

	public static function fetchBalanceAmount($account_id)
	{
		$model = DB::select('select sum(amount) as amount from assets where account_id = ? and asset_type = ?',
			[$account_id, Account::BALANCE]);
        if (isset($model[0]->amount) && $model[0]->amount < 0) {
            throw new TradeException('余额不足');
        }
		return isset($model[0]->amount) ? $model[0]->amount : 0;
	}

	/**
	 * 取默认的解锁时间
	 * @desc fetchUnlockTime
	 * @return false|string
	 */
    public static function fetchUnlockTime()
    {
	    return date('Y-m-d H:i:s',time()+3600*24*365*20);
    }

    public function unlock()
    {
    	return $this->is_lock == 0;
    }

    public function lockText()
    {
    	return $this->unlock() ? '正常' : '冻结';
    }

    public function ismoney()
    {
    	return $this->asset_type == Account::BALANCE;
    }

    public function project()
    {
    	return $this->hasOne(Project::class, 'asset_code', 'asset_type');
    }

    public function account()
    {
    	return $this->hasOne(Account::class, 'id', 'account_id');
    }
}
