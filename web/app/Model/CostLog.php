<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/7/25
 * Time: 下午2:43
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class CostLog extends Model
{
	protected $fillable = [
		'account_id', 'amount', 'asset_type','cost','content'
	];

	public static function record($accout_id, $amount, $asset_type, $cost, $content)
	{
		return static::create([
			'account_id' => $accout_id,
			'amount' => $amount,
			'asset_type' => $asset_type,
			'cost' => $cost,
			'content' => $content
		]);
	}

}