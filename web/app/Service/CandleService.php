<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/7/12
 * Time: 上午11:04
 */

namespace App\Service;


use App\Model\Candle;
use App\Model\TradeLog;

class CandleService
{

	/**
	//            x轴                              时间        均价     涨跌    成交
	{ 'name': '2017/09/09 14:11:11', 'value': ['2017/09/09', 'open, 'close', '100',  '+1%', '99'] },
	 * @desc minValue
	 */
	public function minValue($asset_type, $start, $trades)
	{
		// x轴
		$name = $start;
		$value = [];
		$value[0] = $start;

		// 开盘、收盘
		$open = $trades[0]['price'];
		$close = $trades[count($trades) - 1]['price'];

		/**
		 * 涨幅
		 * 1、求出上一区间收盘价
		 * 2、若无上一区间收盘价，则初始化为0
		 */
		$lastPrice = $this->prevPrice($start, $asset_type, Candle::TYPE_MIN);
		if ($lastPrice == 0) {
			$increase_amount = 0;
			$increase = '0%';
		} else {
			$increase_amount = $close - $lastPrice;
			$increase = round(($increase_amount / $lastPrice) * 100, 2) . '%';
		}

		$amount = 0;
		$count = 0;
		foreach ($trades as $trade) {
			$count += $trade['amount'] * $trade['price'];
			$amount += $trade['amount'];
		}

		$average_price = round($count / $amount, 2);

		return ['name' => $name, 'value' => [$name, $open, $close, $average_price, $increase, $amount]];

	}
	/**
	 * 获取收一个区间的收盘价
	 * @desc prevPrice
	 * @param $time
	 * @param $asset_type
	 * @return int
	 */
	public function prevPrice($time, $asset_type, $type)
	{
		// 如果是分时要取昨收
		if($type == Candle::TYPE_MIN) {
			$time = date('Y-m-d', strtotime($time)) . ' 00:00:00';
		}
		// 如果是分时要取昨收
		$tradeLog = TradeLog::where('asset_type', $asset_type)
			->where('created_at', '<=', $time)
			->orderBy('id', 'desc')
			->select('price')
			->first();
		return isset($tradeLog['price']) ? $tradeLog['price'] : 0;
	}

	/**
	 * @desc candleStartTime
	 * @param $code
	 *     资产编号
	 */
	public function startTime($tradeSet, $type)
	{
		// 1、取上一条计算完的时间
		$candle = Candle::where('asset_code', $tradeSet['asset_type'])
			->where('type', $type)
			->orderBy('id', 'desc')->first();
		if($candle) {
			if($candle['name'] < date('Y-m-d')) {
				// 如果是最后数据是前一天的，取今天的开盘时间

				return date('Y-m-d') . ' ' . $tradeSet['start'];

			} else {
				// 取最后一个区间的下一个下一个区间段
				if($type == Candle::TYPE_DAY) {
					return date('Y-m-d', strtotime($candle['name']) + 86400);
				}

				return date('Y-m-d H:i:s', strtotime($candle['name']) + $type * 60);
			}

		}

		return $type == Candle::TYPE_DAY ? $tradeSet['trade_start'] : $tradeSet['trade_start'] . ' ' . $tradeSet['start'];
	}

	/**
	 * 根据某一时间段内的交易数据计划出K线展示数据
	 * @desc kValue
	 * @param $trades
	 * @return array
	//  日期        开盘        收盘      涨跌     涨幅      最低      最高
	['2015/12/31','3570.47','3539.18','-33.69','-0.94%','3538.35','3580.6', '成交量'],
	 */
	public function kValue($start, $asset_type, $trades, $type)
	{

		/**
		 * 1. 求日期
		 */
		$date = $start;
		if($type == Candle::TYPE_DAY)
		{
			$date = date('Y-m-d', strtotime($start));
		}

		// 开盘、收盘
		$open = $trades[0]['price'];
		$close = $trades[count($trades) - 1]['price'];

		/**
		 * 涨幅
		 * 1、求出上一区间收盘价
		 * 2、若无上一区间收盘价，则初始化为0
		 */
		$lastPrice = $this->prevPrice($start, $asset_type, $type);
		if ($lastPrice == 0) {
			$increase_amount = 0;
			$increase = '0%';
		} else {
			$increase_amount = $close - $lastPrice;
			$increase = round(($increase_amount / $lastPrice) * 100, 2) . '%';
		}

		// 最高最低
		$low = $trades[0]['price'];
		$high = $trades[0]['price'];
		$amount = 0;
		foreach ($trades as $trade) {
			$trade['price'] < $low && $low = $trade['price'];
			$trade['price'] > $high && $high = $trade['price'];
			$amount += $trade['amount'];
		}

		return [$date, $open, $close, $increase_amount, $increase, $low, $high, $amount];

	}


}