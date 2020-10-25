<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/7/24
 * Time: 上午10:41
 */

namespace App\Service;

use App\Model\TradeSet;
use Carbon\Carbon;

class TradeSetService
{

	/**
	 * @desc isInTradeTime
	 * @param $trade   TradeSet || int
	 *           trade model or trade.asset_type
	 * @return bool
	 */
	public static function isInTradeTime($trade)
	{
		return false;
		$now = Carbon::now();
		if(!$now->isWeekday() || date('Ymd') == "20180101") {
			return false;
		}
		if (!$trade instanceof TradeSet && !is_array($trade)) {
			$trade = TradeSet::fetchOne($trade);
		}

		if (empty($trade) || !isset($trade['start'])) {
			return false;
		}

		$now = date('H:i:s');

		return ($now > $trade['start'] && $now < $trade['end'])  || ($now > $trade['start2'] && $now < $trade['end2']);

	}

	public static function isStartTrade($trade)
	{
		if (!$trade instanceof TradeSet && !is_array($trade)) {
			$trade = TradeSet::fetchOne($trade);
		}

		if (empty($trade) || !isset($trade['start'])) {
			return false;
		}

		return date('Y-m-d') > $trade['trade_start'];
	}

}