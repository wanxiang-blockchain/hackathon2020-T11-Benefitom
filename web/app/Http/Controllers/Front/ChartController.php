<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/7/10
 * Time: 下午6:21
 */

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Model\Asset;
use App\Model\AssetType;
use App\Model\Candle;
use App\Model\Project;
use App\Model\TradeLog;
use App\Model\TradeSet;
use App\Service\CandleService;
use App\Service\TradeService;
use App\Service\ValidatorService;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
	protected $validatorService;
	protected $request;

	public function __construct(ValidatorService $_validator)
	{
		$this->validatorService = $_validator;
	}
	/**
	 * 分时图
	 * @desc min
	 * @return array
	 */
	public function min(Request $request, CandleService $candleService)
	{

		$code = $request->get('code', '');

		if(empty($code)) {
			return ['code' => 201, 'data' => '参数不正确'];
		}

		$asset_type = AssetType::where('code', $code)->first();
		if(empty($asset_type)) {
			return ['code'=>201, 'data'=>[]];
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

	/**
	 * K 线图
	 * @desc k
	 */
	public function k(Request $request, CandleService $candleService)
	{

		$code = $request->get('code', '');
		$type = intval($request->get('type', ''));

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

		$before = 60;
		switch (intval($type)) {
			case 5:
				$before = 3;
				break;
			case 10:
				$before = 5;
				break;
			case 15:
				$before = 7;
				break;
			case 30:
				$before = 14;
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

	/**
	 * 首页行情
	 * @desc trade_table
	 * @param $method
	 * @param $parameters
	 * @return {
	code: '001', name: '心经', price: '10.9', increase:'4.8', increase_amount:'0.1', total_amount: 190, total_balance: 1900, change: '2'
	}
	 */
	public function tradeTable(TradeService $tradeService, Request $request)
	{
		// 取出所有在交易中作品
		$limit = $request->get('limit', 3);
		$query = TradeSet::where('trade_start', '<=', date('Y-m-d'));
		$lastId = $request->get('lastId');
		if($lastId){
			 $query->where('id', '>', $lastId);
		}
		$tradeSets = $query->limit($limit)->get();
		if(count($tradeSets) <= 0){
			$tradeSets = TradeSet::where('trade_start', '<=', date('Y-m-d'))->limit($limit)->get();
		}
		if (empty($tradeSets)) {
			return ['code' => 201, 'data' => 'no data'];
		}
		$ret = [];
		foreach ($tradeSets as $t) {
			$lastId = $t['id'];
			// 取上一个日k数据
			$lastCandle = Candle::where('asset_code', $t['asset_type'])
				->where('type', Candle::TYPE_DAY)
				->select(['name', 'value'])
				->orderBy('id', 'desc')
				->first();
			// 取今日交易数据
			$lastTrade = TradeLog::where('asset_type', $t['asset_type'])
				->where('created_at', '>', date('Y-m-d 00:00:00'))
				->orderBy('id', 'desc')
				->first();
			// 取asset
			$asset = AssetType::where('code', $t['asset_type'])->first();
			if (!$asset) {
				continue;
			}
			$lastPrice = $tradeService->lastPrice($t['asset_type']);
			if($lastCandle) {
				$lastCandleTrade = json_decode($lastCandle['value'], true);
				$lastPrice = $lastCandleTrade[2];
			}
			// 取 project
			$project = Project::where('asset_code', $t['asset_type'])->select('total')->first();

			if (!$lastTrade) {
				// 今日无交易
				if(!$lastCandle) {
					// 也无前交易记录
					$ret[] = [
						'id' => $asset['id'],
						'code' => $t['asset_type'],
						'name' => $asset['name'],
						'price' => $lastPrice,
						'increase' => 0.00,
						'increase_amount' => 0.00,
						'total_amount' => 0,
						'total_balance' => 0,
//						'change' => 0
					];
					continue;
				}
				$ret[] = [
					'id' => $asset['id'],
					'code' => $t['asset_type'],
					'name' => $asset['name'],
					'price' => $lastPrice,
					'increase' => number_format(round($lastCandleTrade[2] - $lastCandleTrade[1], 2), 2),
					'increase_amount' =>  number_format(round(($lastCandleTrade[2] - $lastCandleTrade[1]) / $lastCandleTrade[2], 2)  * 100, 2),
					'total_amount' => $lastCandleTrade[7],
					'total_balance' => round($lastCandleTrade[8], 2),
//					'change' => round($lastCandleTrade[7] / $project['total'] * 100, 2)
				];
				continue;
			}
			// 今日有交易，取出今日所有交易
			$results = DB::select('select sum(total) as total, sum(amount) as amount from trade_logs where asset_type = ? and created_at > ?', [$t['asset_type'], date('Y-m-d 00:00:00')]);
			$ret[] = [
				'id' => $asset['id'],
				'code' => $t['asset_type'],
				'name' => $asset['name'],
				'price' => $lastTrade['price'],
				'increase' => number_format(round($lastTrade['price'] - $lastPrice, 2), 2),
				'increase_amount' => number_format(round(($lastTrade['price'] - $lastPrice) / $lastPrice, 4) * 100, 2),
				'total_amount' => $results[0]->amount,
				'total_balance' => $results[0]->total,
//				'change' => round($results[0]->amount / $project['total'] * 100, 2)
			];
		}
		return ['code' => 200, 'data' => $ret, 'lastId' => $lastId];
	}

}