<?php

namespace App\Http\Controllers\Front;

use App\Model\Asset;
use App\Model\AssetType;
use App\Model\Member;
use App\Model\Project;
use App\Model\TradeLog;
use App\Model\TradeOrder;
use App\Model\TradeSet;
use App\Model\Ws;
use App\Service\TradeSetService;
use Detection\MobileDetect;
use Illuminate\Http\Request;
use App\Model\Account;
use App\Http\Controllers\Controller;
use App\Service\AccountService;
use App\Service\TradeService;
use Auth;
use Carbon\Carbon;

class TradeController extends Controller
{
    function __construct(TradeService $tradeService) {
        $this->tradeService = $tradeService;
    }

	public function index(Request  $request)
	{
            $asset_types = AssetType::where('code', '!=', Account::BALANCE)
	            ->rightJoin('trade_set', 'trade_set.asset_type', '=', 'asset_types.code')
	            ->where('trade_set.trade_start', '<=', date('Y-m-d'))
	            ->select('asset_types.*')
                ->get();

            //shortcut 如果只有一种交易资产， 直接跳转
            if (count($asset_types) == 1) {
                return redirect("/trade/detail/" . $asset_types[0]->id);
            }

            $nowDay = Carbon::now();
            $yDay   = Carbon::yesterday();
            foreach($asset_types as $k => $asset_type){
                $trade = TradeLog::where('asset_type',$asset_type->code)
                    ->whereBetween('created_at',[$yDay, $nowDay])
                    ->orderBy('created_at','desc')
                    ->get()
                    ->toArray();
                $asset_types[$k]['trade']       = empty($trade) ? "" : array_sum(array_column($trade,'amount'));
                $asset_types[$k]['turnover']    = empty($trade) ? "" : array_sum(array_column($trade,'price'));
                $asset_types[$k]['latestPrice'] = empty($trade) ? "" : isset($trade['0']) ? $trade['0']['price'] : "";
                $asset_types[$k]['totalMarket'] = empty($trade) ? "" : (100000000 * $asset_types[$k]['latestPrice']);

            }
            return view('front.trade.index',compact('asset_types'));

    }

	public function mindex(Request  $request)
	{
		return view('front.trade.mindex');
	}


    public function ajaxDetail($id, TradeService $tradeService, AccountService $accountService)
    {
	    $nowDay = date('Y-m-d H:i:s',time());
	    $member_id = Auth::guard('front')->id();
	    // 余额
	    $balance = $accountService->balance($member_id);
	    $asset_type = AssetType::find($id);
	    if(empty($asset_type)) {
	    	return ['code' => '201'];
	    }
	    $code = $asset_type->code;
	    // 委托买入卖出
	    list($buy_trades, $sale_trades) = $this->tradeService->trades($code);

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

	    $bestBuy = number_format($bestBuy, 2);
	    $bestSell = number_format($bestSell, 2);

	    // 买盘委托
	    $buyOrderCount = intval($buy_trades[0]['amount']);

	    // 卖盘委托
	    $sellOrderCount = intval($sale_trades[4]['amount']);

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
	    $increase = number_format(round(($latestPrice - $trade_price) /  $trade_price * 100, 2), 2) . '%';
	    $increase_amount = number_format(round($latestPrice - $trade_price, 2), 2);
	    $asset_name = $asset_type->name;
	    $summary = compact('asset_name','buyOrderCount', 'sellOrderCount', 'sumPrice', 'maxPrice', 'bestBuy', 'bestSell',
		    'latestPrice', 'minPrice', 'openPrice', 'trade_price', 'sumAmount', 'increase', 'increase_amount');
	    $buySell = compact('balance', 'bestSell', 'bestBuy', 'hold_amount', 'maxBuy', 'maxSell');

	    return [
	    	'code' => 200,
		    'data' => compact('buy_trades', 'sale_trades', 'trade_logs', 'buySell', 'my_entrust', 'summary')
	    ];
    }

    //测试环境下访问detail_dev
    public  function detail($id, TradeService $tradeService, AccountService $accountService){
        $nowDay = date('Y-m-d H:i:s',time());
	    $member_id = Auth::guard('front')->id();
	    $balance = $accountService->balance($member_id);
	    $asset_type = AssetType::find($id);
	    if(empty($asset_type)) {
	    	return redirect('trade');
	    }
	    $hold_amount = intval($accountService->assetAmount($member_id, $asset_type['code']));
	    $code = $asset_type->code;
	    $project = Project::where('asset_code', $code)->first();
        $trade_limit = TradeSet::where('asset_type',$code)->pluck('limit')->first();
        // 昨日收盘价
        $trade_price = $tradeService->lastPrice($code);
        //涨停
        $asset_type['rise_limit'] = round($trade_price - $trade_price*$trade_limit,2);
        //跌停
        $asset_type['fall_limit'] = round($trade_price + $trade_price*$trade_limit,2);
	    // 持有量
        $first_order = TradeLog::where('asset_type',$code)->whereBetween('created_at',[Carbon::today(),$nowDay])->orderBy('id')->select('id','price','amount')->get()->toarray();
        //今日开盘
        $asset_type['openPrice'] = isset($first_order['0']) ? $first_order['0']['price'] : "";
        // 只取当日
        $tradeLogs = TradeLog::where('asset_type',$code)
	        ->where('created_at', '>', date('Y-m-d ') . '00:00:00')->orderBy('id','desc')->limit(8)->get();
	    $trade_log  = TradeLog::where('asset_type',$code)->select('price','amount','total')->get()->toArray();
	    $prices = array_column($trade_log,'price');
	    $total = array_column($trade_log,'total');
        // 最新价
        $asset_type['latestPrice'] = isset($tradeLogs['0']) ? $tradeLogs['0']['price'] : $trade_price;
        //涨跌
        $asset_type['ups_down'] = empty($asset_type['latestPrice']) ? 0 :  $asset_type['latestPrice'] - $trade_price;
        //涨幅
        $asset_type['ups_fu'] = round($asset_type['ups_down']/ $trade_price,4) * 100;
        //现手
        $asset_type['the_hand'] = empty($tradeLogs->toarray()) ? "": $tradeLogs['0']['amount'] ;
        //总手
        $asset_type['sum_hand'] = array_sum(array_column($first_order,'amount'));
        //换手
        $sum_amount = array_sum(array_column($trade_log,'amount'));
        $asset_type['change_hand'] = round($sum_amount/20000000,6) * 100;
        //最佳买价
	    $asset_type['bestBuy'] =  TradeOrder::where([
            ['asset_type', '=' ,$code ],
            ['type','=',2]])
            ->where(function ($query) {
                $query->where('status', 0)
                    ->orwhere('status', 1);
            })->min('price');
	    empty($asset_type['bestBuy']) &&  $asset_type['bestBuy'] = $trade_price;
	    //最佳卖价
        $asset_type['bestSell'] =  TradeOrder::where([
            ['asset_type', '=' ,$code ],
            ['type','=',1]])
            ->where(function ($query) {
                $query->where('status', 0)
                    ->orwhere('status', 1);
            })->max('price');
        empty($asset_type['bestSell']) &&  $asset_type['bestSell'] = $trade_price;
        $member_id = \Auth::guard('front')->id();
        //我的委托
        $myTrades = TradeOrder::where([
            ['asset_type', '=' ,$code ],
            ['member_id', '=' ,$member_id]])
            ->where(function ($query) {
                $query->where('status', 0)
                    ->orWhere('status', 1);
            })->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $trade_set = TradeSet::where('asset_type',$asset_type->code)->select('start','end', 'start2', 'end2', 'rate','limit','trade_start')->first();
        if(!empty($trade_set)){
            if($trade_set->trade_start > Carbon::today()->toDateString()){
                $begin = 1;
            } else {
                $begin = 0;
            }
        }else{
               $begin = 1;
        }
        $key = trim(file_get_contents("../rsa_1024_pub.pem"));

        // 生成wstoken
	    $wstoken = Ws::add($member_id, $id);

	    // 是否在交易时段
	    $isInTradeTime = TradeSetService::isInTradeTime($trade_set);

	    $prev = AssetType::prev($id);
	    $next = AssetType::next($id);

	    $view = 'detail_dev';
	    $detect = new MobileDetect();
	    // Any mobile device (phones or tablets).
	    if ( $detect->isMobile() ) {
		    $view = 'trade';
	    }

	    return view('front.trade.'.$view,compact('prev', 'next', 'myTrades', 'hold_amount', 'asset_type', 'begin','key', 'project', 'balance', 'trade_set', 'wstoken', 'isInTradeTime'));
    }

	/**
	 * @desc tradeOrder
	 * @param AccountService $accountService
	 * @param TradeService   $tradeService
	 * @param $_POST
	 *      type: 1买 2卖
	 *      asset_type： 产品代码
	 *      buyPrice:  委托买入价格
	 *      butAmount:  委托买入数量
	 *      sellPrice:  委托卖出价
	 *      sellAmount: 委托卖出数量
	 * @return array
	 */
    function tradeOrder(AccountService $accountService,TradeService $tradeService){
        $trade = Request()->all();
        $member_id = Auth::guard('front')->id();
        $trade_set = TradeSet::where('asset_type',$trade['asset_type'])->select('start','end', 'start2', 'end2', 'rate','limit','trade_start', 't_plus')->first()->toarray();
        if($trade_set['trade_start'] > Carbon::today()->toDateString()){
            return ['code'=>230,'data'=>'交易中心尚未开启,请先认购 <a target="_self" href="/subscription">去认购</a>'];
        }
        $rate = $trade_set['rate'];
        if(empty($member_id)){
            return ['code'=>201, 'data'=>'请先登录, <a target="_self" href="/login">去登录</a>'];
        }
        $member = Account::where(['member_id'=>$member_id])->first();
        if(empty($member->trade_pwd)){
            return ['code'=>220,'data'=>'请先设置交易密码, <a target="_self" href="/member/resetTradePassword">去设置</a>'];
        }

        // 半小时内可使用交易remember_token
	    if (!isset($trade['remember_token']) || !$accountService->checkRemberToken($member->id, $trade['remember_token']) ) {

		    $decrypted = "";
		    if (openssl_private_decrypt(base64_decode($trade['tradePassword']),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
			    $trade['tradePassword'] = $decrypted;
		    }

		    if(!(\Hash::check($trade['tradePassword'], $member->trade_pwd))) {
			    return ['code'=>202, 'data'=>'交易密码不正确'];
		    }

	    }

	    // 每次刷新remember_token
	    $trade['remember_token'] = $accountService->createRemeberToken($member->id);

        $now = Carbon::now();
        $start = $trade_set['start'];
        $end =$trade_set['end'];
        // 交易范围，左闭，右闭
        if(!($now->isWeekday() && TradeSetService::isInTradeTime($trade_set))
            || date('m-d') == '05-01'){
            return ['code'=>203, 'data'=>'不在交易时间范围'];
        }

        $trade_price = $tradeService->lastPrice($trade['asset_type']);
        $price['min'] = round($trade_price - $trade_price*$trade_set['limit'],2);
        $price['max'] = round($trade_price + $trade_price*$trade_set['limit'],2);
        if($trade['type'] == 1){
            $result = preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $trade['buyPrice']);
            if(!$result){
                return ['code'=>220,'data'=>'价格最多保留两位小数点'];
            }
            $trade['buyPrice'] = ltrim($trade['buyPrice'],'0');
            if(!preg_match("/^[1-9][0-9]*$/",$trade['buyAmount'])){
                return ['code'=>250,'data'=>'数量必须为正整数'];
            }
            $trade['buyAmount'] = intval($trade['buyAmount']);
            if($trade['buyPrice']<$price['min'] || $trade['buyPrice']>$price['max']){
                return ['code'=>208,'data'=>'超过涨跌停限制或范围'];
            }
            $balance = $accountService->balance($member_id);
            if(($trade['buyAmount'] * $trade['buyPrice'])>$balance){
                return ['code'=>204, 'data'=>$balance];
            }
            try{
                $ret = $tradeService->makeOrder($trade['type'],$trade['asset_type'],$member_id,$trade['buyAmount'],$trade['buyPrice'],$rate);
	            $ret['remember_token'] = $trade['remember_token'];
	            return $ret;
            }catch (\Exception $e){
            	\Log::error($e->getTraceAsString());
                return ['code'=>2210, 'data'=>$e->getMessage()];
            }
        }else{
            $result = preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $trade['sellPrice']);
            if(!$result){
                return ['code'=>220,'data'=>'价格最多保留两位小数点'];
            }
            $trade['sellPrice'] = ltrim($trade['sellPrice'],'0');
            if(!preg_match("/^[1-9][0-9]*$/",$trade['sellAmount'])){
                return ['code'=>250,'data'=>'数量必须为正整数'];
            }
            $trade['sellAmount'] = intval($trade['sellAmount']);
            if($trade['sellPrice']<$price['min'] || $trade['sellPrice']>$price['max']){
                return ['code'=>208,'data'=>'超过涨跌停限制或范围'];
            }
            $sum = $accountService->tPlusAssetAmount($member_id,$trade['asset_type'], $trade_set['t_plus']);
            if(($trade['sellAmount']>$sum)){
                return ['code'=>204, 'data'=>'您的可卖数量不足,当前还剩'.intval($sum).'个， 可前往我的资产查看'];
            }
            try{
               $ret = $tradeService->makeOrder($trade['type'],$trade['asset_type'],$member_id,$trade['sellAmount'],$trade['sellPrice'],$rate);
               $ret['remember_token'] = $trade['remember_token'];
               return $ret;
            }catch (\Exception $e){
	            \Log::error($e->getTraceAsString());
                return ['code'=>210, 'data'=>$e->getMessage()];
            }
        }

    }

    function revoked(TradeService $tradeService){
        $order_id = Request()->input('order_id');
        $member_id = Auth::guard('front')->user()->id;
        $trade = TradeOrder::where('member_id',$member_id)->where('id',$order_id)->first();
        if(empty($trade)){
            return ['code'=>202,'data'=>'订单不存在'];
        }
        if($trade->status == 2 || $trade->status ==  3){
            return ['code'=>202,'data'=>'该订单已撤销或已完成'];
        }
        $status = $tradeService->cancelOrder($order_id);
        if($status != -1){
            return ['code' => 200,'data' => '成功'];
        }else{
            return ['code' => 201,'data' => '失败'];
        }
    }

	public function myentrust($code)
	{
		$member = Member::current();

		//我的委托
		$models = TradeOrder::where([
			['asset_type', '=' ,$code ],
			['member_id', '=' ,$member->id]])
			->where(function ($query) {
				$query->where('status', 0)
					->orWhere('status', 1);
			})->orderBy('created_at', 'desc')
			->get();
		return view('front.trade.myentrust', compact('models'));
	}
}
