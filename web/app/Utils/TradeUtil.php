<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/9/27
 * Time: 下午2:45
 */

namespace App\Utils;


use App\Model\Asset;
use App\Model\AssetType;
use App\Model\TradeLog;
use App\Model\TradeOrder;
use App\Model\Ws;
use App\Service\AccountService;
use App\Service\FinanceService;
use App\Service\TradeService;
use Carbon\Carbon;

class TradeUtil
{

	public static function wsDetail($wstoken)
	{
		$wsmodel = Ws::where('tk', $wstoken)->first();
		if(empty($wsmodel)) {
			return ['code' => '209', 'data' => 'error params'];
		}
		$accountService = new AccountService();
		$financeService = new FinanceService();
		$tradeService = new TradeService($accountService, $financeService);
		$nowDay = date('Y-m-d H:i:s',time());
		$member_id = $wsmodel->member_id;
		// 余额
		$balance = $accountService->balance($member_id);
		$asset_type = AssetType::find($wsmodel->asset_type_id);
		if(empty($asset_type)) {
			return ['code' => '201'];
		}
		$code = $asset_type->code;
		// 委托买入卖出
		list($buy_trades, $sale_trades) = $tradeService->trades($code);

		// 昨日收盘价
		$trade_price = $tradeService->lastPrice($code);

		//我的委托
		$my_entrust = TradeOrder::where([
			['asset_type', '=' ,$code ],
			['member_id', '=' ,$member_id]])
			->where(function ($query) {
				$query->where('status', 0)
					->orWhere('status', 1);
			})->orderBy('created_at', 'desc')
			->get();

		// 只取当日，作为成交记录展示
		$trade_logs = TradeLog::where('asset_type',$code)
			->select(['created_at', 'price', 'amount'])
			->where('created_at', '>', date('Y-m-d ') . '00:00:00')->orderBy('id','desc')->limit(8)->get();

		// 记录成交额成交量
		$trade_log = TradeLog::where('asset_type',$code)->where('created_at', '>', date('Y-m-d 00:00:00'))->select('price','amount','total')->get()->toArray();
		// 成交额
		$sumPrice = 0;
		// 成交量
		$sumAmount = 0;
		$maxPrice = 0;
		$minPrice = 0;
		foreach ($trade_log as $log){
			$log['price'] > $maxPrice && $maxPrice = $log['price'];
			($log['price'] < $minPrice || $minPrice == 0) && $minPrice = $log['price'];
			$sumPrice += $log['total'];
			$sumAmount += $log['amount'];
		}
		//	    $sumAmount = number_format($sumAmount, 2);
		// 开盘
		$first_order = TradeLog::where('asset_type',$code)->whereBetween('created_at',[Carbon::today(),$nowDay])->orderBy('id')->select('id','price','amount')->get()->toarray();
		//今日开盘
		$openPrice = isset($first_order[0]) ? $first_order[0]['price'] : $trade_price;
		// 最新价
		$latestPrice = isset($trade_logs[0]) ? $trade_logs[0]['price'] : $trade_price;

		//最佳买价
		$bestBuy =  TradeOrder::where([
			['asset_type', '=' ,$code ],
			['type','=',2]])
			->where(function ($query) {
				$query->where('status', 0)
					->orwhere('status', 1);
			})->min('price');
		empty($bestBuy) &&  $bestBuy = $latestPrice;

		//最佳卖价
		$bestSell =  TradeOrder::where([
			['asset_type', '=' ,$code ],
			['type','=',1]])
			->where(function ($query) {
				$query->where('status', 0)
					->orwhere('status', 1);
			})->max('price');

		empty($bestSell) &&  $bestSell = $latestPrice;

		$bestBuy = round($bestBuy, 2);
		$bestSell = round($bestSell, 2);

		// 持有量
		//	    $hold_amount = intval($accountService->assetAmount($member_id, $asset_type['code']));
		$hold_amount = Asset::where('asset_type', $asset_type['code'])
			->where('account_id', $accountService->getAccountId($member_id))
			->where('is_lock', 0)
			->sum('amount');
		// 最大可卖
		$maxSell = $accountService->tPlusAssetAmount($member_id, $asset_type['code']);
		// 最大可买
		$maxBuy = intval($balance / $bestBuy);
		//	    $balance = number_format($balance, 2);

		// 涨幅
		$increase = round(($latestPrice - $trade_price) /  $trade_price * 100, 2) . '%';
		$summary = compact('sumPrice', 'maxPrice', 'latestPrice', 'minPrice', 'openPrice', 'trade_price', 'sumAmount', 'increase');
		$buySell = compact('balance', 'bestSell', 'bestBuy', 'hold_amount', 'maxBuy', 'maxSell');

		return [
			'code' => 200,
			'data' => compact('buy_trades', 'sale_trades', 'trade_logs', 'buySell', 'my_entrust', 'summary')
		];
	}

}