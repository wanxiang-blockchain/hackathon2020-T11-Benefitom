<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/9/26
 * Time: 下午4:04
 */

namespace App\Utils;


use App\Model\AssetType;
use App\Model\Candle;
use App\Model\TradeLog;
use App\Model\TradeSet;
use App\Service\CandleService;

class ChartUtil
{

	public static function min($code)
	{
		$candleService = new CandleService();

		if(empty($code)) {
			return ['code' => 201, 'data' => '参数不正确'];
		}

		$asset_type = AssetType::where('code', $code)->first();
		if(empty($asset_type)) {
			return ['code'=>201, 'data'=>['不存在']];
		}

		// 取出已计算好的分时
		$candles = Candle::where('asset_code', $code)
			->where('type', Candle::TYPE_MIN)
			->where('name', '>', date('Y-m-d 00:00:00'))
			->select('name', 'value')
			->get()
			->all();

		$data = [];
		foreach ($candles as $candle) {
			$data[] = json_decode($candle['value'], true);
		}

		$start = date('Y-m-d H:i:00');
		$end = date('Y-m-d H:i:s');

		// 取出未计算的新数据
		$trades = TradeLog::where('asset_type', $code)
			->where('created_at', '>', $start)
			->where('created_at', '<=', $end)
			->select('amount', 'price')
			->get()
			->all();

		if($trades) {
			$data[] = $candleService->minValue($code, $start, $trades);
		}
		return ['code' => 200, 'data' => $data];

	}


	public static function k($code, $type)
	{
		$candleService = new CandleService();

		if(empty($code)) {
			return ['code' => 201, 'data' => '参数不正确'];
		}

		if(!in_array($type, [0, 5, 10, 15, 30, 60])) {
			return ['code' => 201, 'data' => '参数不正确'];
		}

		$asset_type = AssetType::where('code', $code)->first();
		if(empty($asset_type)) {
			return ['code'=>201, 'data'=>[]];
		}

		$before = 160;
		switch (intval($type)) {
			case 5:
				$before = 13;
				break;
			case 10:
				$before = 15;
				break;
			case 15:
				$before = 17;
				break;
			case 30:
				$before = 24;
				break;
			case 60:
				$before = 30;
				break;
		}

		// 取出已计算好的数据
		$candles = Candle::where('asset_code', $code)
			->where('type', $type)
			->where('name', '>', date('Y-m-d 00:00:00', time() - 86400 * $before))
			->select('name', 'value')
			->get()
			->all();

		$data = [];
		foreach ($candles as $candle) {
			$data[] = json_decode($candle['value'], true);
		}

		// 取当日数据
		$tradeSet = TradeSet::where('asset_type', $code)
			->first();

		$start = $candleService->startTime($tradeSet, $type);
		$end = date('Y-m-d H:i:s');

		// 取出未计算的新数据
		$trades = TradeLog::where('asset_type', $code)
			->where('created_at', '>', $start)
			->where('created_at', '<=', $end)
			->select('amount', 'price')
			->get()
			->all();

		if($trades) {
			$data[] = $candleService->kValue($start, $code, $trades, $type);
		}

		// 如果数据不足20条，补齐20条
		$yu = 20 - count($data);
		if($yu > 0) {
			for(; $yu>0; $yu--) {
				$data[] = [''];
			}
		}

		return ['code' => 200, 'data' => $data];
	}

}