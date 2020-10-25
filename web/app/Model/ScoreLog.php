<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ScoreLog extends Model
{
	/**
	 * 积分来源
	 */
	const TYPE_TRADE_PROFIT = 1; // 交易利润
	const TYPE_TRADE_shop   = 2; // 商城支出

	protected $fillable = [
		'score', 'account_id', 'order_id', 'type'
	];

}
