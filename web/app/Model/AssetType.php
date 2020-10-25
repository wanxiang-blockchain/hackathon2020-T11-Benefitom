<?php

namespace App\Model;

use App\Service\TradeSetService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AssetType extends Model
{
    protected $fillable = [
        'code', 'name', 'desc','market_value'
    ];

    public static function updateValue($data, $value)
    {
    	$code = $data['asset_code'];
    	$asset = AssetType::where('code', $code)->orwhere('name', '=', $data['name'])->first();
	    if (empty($asset)) {
	    	AssetType::create(["code" => $code, "name" => $data['name']]);
	    }
	    AssetType::where('code', $code)->orwhere('name', '=', $data['name'])->update([
	    	'market_value' => $value,
		    'code' => $code,
		    'name' => $data['name']
		]);
    }

    public static function allExceptBalance()
    {
    	return static::where('code', '!=', Account::BALANCE)->all();
    }

    public function tradeSet()
    {
    	return $this->hasOne(TradeSet::class, 'asset_type', 'code');
    }

    public function project()
    {
    	return $this->hasOne(Project::class, 'asset_code', 'code');
    }

    public static function prev($id)
    {
	    return AssetType::where('code', '!=', Account::BALANCE)
		    ->rightJoin('trade_set', 'trade_set.asset_type', '=', 'asset_types.code')
		    ->where('trade_set.trade_start', '<=', date('Y-m-d'))
		    ->where('asset_types.id', '<', $id)
		    ->select('asset_types.id')
		    ->first();
    }

	public static function next($id)
	{
		return AssetType::where('code', '!=', Account::BALANCE)
			->rightJoin('trade_set', 'trade_set.asset_type', '=', 'asset_types.code')
			->where('trade_set.trade_start', '<=', date('Y-m-d'))
			->where('asset_types.id', '>', $id)
			->select('asset_types.id')
			->first();
	}
}
