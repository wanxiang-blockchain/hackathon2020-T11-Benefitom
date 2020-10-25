<?php
namespace App\Service;

use App\Model\Asset;
use App\Model\Project;
use App\Model\TradeSet;
use Carbon\Carbon;
use App\Model\AssetType;
use DB;
use App\Model\Account;
use App\Model\TradeOrder;
use App\Model\TradeLog;
use App\Exceptions\NotEnough;
use Illuminate\Support\Facades\Log;


class TradeService
{

    function __construct(AccountService $accountService, FinanceService $financeService)
    {
        $this->accountService = $accountService;
        $this->financeService = $financeService;
    }

    /**
     *   建立一个委托单
     *   $trade_type 1 为买入 2 为卖出
     *   $asset_type 为资产编号
     *   $member_id    订单所有人
     *   $amount   挂单数量
     *   $price      挂单价格
     *   $rate      手续费率
     */
    function makeOrder($trade_type, $asset_type, $member_id, $amount, $price,$rate)
    {
        DB::beginTransaction();
	    try{
		    if ($trade_type == 1) {
			    $status = $this->makeBuyOrder($asset_type, $member_id, $amount, $price,$rate);
		    } elseif ($trade_type == 2) {
			    $status =  $this-> makeSellOrder($asset_type, $member_id, $amount, $price,$rate);
		    }
		    DB::commit();
		    return $status;
	    }catch (\Exception $e){
	    	DB::rollBack();
		    Log::error($e->getTraceAsString());
	    	throw new \Exception($e->getMessage());
	    }
    }

    private function makeSellOrder($asset_type, $member_id, $amount, $price,$rate)
    {
	    $orders = TradeOrder::lockForUpdate()
		    ->where([
			    ['type', '=', 1],
			    ['price', '>=', $price],
			    ['asset_type','=',$asset_type]])
		    ->where(function ($query) {
			    $query->where('status', 0)
				    ->orwhere('status', 1);
		    })->orderBy('price', 'desc')
		    ->orderBy('created_at', 'asc')
		    ->get();

        $Trade = TradeOrder::create([
            'type'       => 2,
            'asset_type' => $asset_type,
            'member_id'  => $member_id,
            'quantity'   => $amount,
            'amount'     => $amount,
            'price'      => $price,
            'status'     => 0
        ]);
        $trade_id = $Trade->id;
        if(empty($trade_id)) {
        	throw new \Exception('委托失败，请稍候再试');
        }

        if(!empty($orders->toArray())){
            $surplus_amount = $amount;
            foreach ($orders as $order) {
                if ($surplus_amount <= 0) break;
                $seller_id = $member_id;
                $buyer_id = $order->member_id;
                if ($order->amount <= $surplus_amount) {
                    $surplus_amount -= $order->amount;
                    $cur_amount = $order->amount;
                    $order->amount = 0;
                    $order->status = 2;
                    $order->save();
                } else {
                    $order->status = 1;
                    $order->amount -= $surplus_amount;
                    $order->save();
                    $cur_amount = $surplus_amount;
                    $surplus_amount = 0;
                }
	            // 取出买家卖家原持有资产，冻结的也要取
	            $buy_account_id = $this->accountService->getAccountId($buyer_id);
	            $sale_account_id = $this->accountService->getAccountId($seller_id);
	            $buy_asset = Asset::fetchAssetData($buy_account_id, $asset_type);
	            $sale_asset = Asset::fetchAssetData($sale_account_id, $asset_type);

                $this->accountService->transfer($buyer_id, $seller_id, Account::BALANCE, round($order->price * $cur_amount,2),$order->id,$rate);
                $this->accountService->transferTo($seller_id, $buyer_id, $asset_type, $cur_amount);
                $asset = AssetType::where('code',$asset_type)->first();
                $this->addRecharge($asset,$buyer_id,$seller_id,$order->price,$cur_amount);
                // 交易手续费
	            $poundage = $this->accountService->fetchPoundage($order->price, $cur_amount, $rate);
	            // 扣除卖家交易手续费
                $this->accountService->fee($seller_id, Account::BALANCE, $order->price * $cur_amount * $rate);
                TradeLog::create([
                    'asset_type' => $asset_type,
                    'type'       => 1,
                    'buyer_id'   => $buyer_id,
                    'seller_id'  => $seller_id,
                    'amount'     => $cur_amount,
                    'price'      => $order->price,
                    'total'      => $cur_amount * $order->price
                ]);
                // 刷新股价
                $AssetType = AssetType::where('code',$asset_type)->first();
                $AssetType->market_value = $order->price;
                $AssetType->save();
	            // 计算双方成本价
	            $this->accountService->buyCost($buy_asset['cost'], $buy_asset['amount'], $order->price, $cur_amount, $buy_account_id, $asset_type);
	            if($seller_id == $buyer_id) {
	            	// 如果是自己交易，重取成本及持有，因买入会有影响
		            $sale_asset = Asset::fetchAssetData($sale_account_id, $asset_type);
		            $sale_asset['amount'] = $buy_asset['amount'] + $cur_amount;
	            }
	            $this->accountService->saleCost($sale_asset['cost'], $sale_asset['amount'], $order->price, $cur_amount, $sale_account_id, $asset_type, $seller_id, $poundage);

            }
            $bAccount_id = $this->accountService->getAccountId($member_id);
            //如果有剩余
            $Trade = TradeOrder::find($trade_id);
            if($surplus_amount >= 0){
                $Trade->amount = $surplus_amount;
                if($surplus_amount == 0){
                    $Trade->status = 2;
                    $Trade->save();
                    return ['code'=>200,'data'=>'委托卖出成功'];
                }else{
                    $this->accountService->addFreeze($bAccount_id,$asset_type,$surplus_amount,1,$trade_id);
                    $Trade->status = 1;
                    $this->frozenAssets($member_id,$asset,$surplus_amount);
                    $Trade->save();
                    return ['code'=>200,'data'=>'委托部分成交'];
                }
            }
        }else{
            $bAccount_id = $this->accountService->getAccountId($member_id);
            $this->accountService->addFreeze($bAccount_id,$asset_type,$amount,1,$trade_id);
            $asset = AssetType::where('code',$asset_type)->first();
            $this->frozenAssets($member_id,$asset,$amount);
            return ['code'=>200,'data'=>'挂单委托成功'];
        }
    }

    private function makeBuyOrder($asset_type, $member_id, $amount, $price,$rate)
    {
	    // lockForUpdate 表级锁 todo ? 在此处 Deadlock found when trying to get lock; try restarting transaction 但是没有抛出异常？或者异常没有捕获到？
	    $orders = TradeOrder::lockForUpdate()
		    ->where([
			    ['type', '=', 2],
			    ['price', '<=', $price],
			    ['asset_type', '=' ,$asset_type ]])
		    ->where(function ($query) {
			    $query->where('status', 0)
				    ->orwhere('status', 1);
		    })->orderBy('price', 'asc')
		    ->orderBy('created_at', 'asc')
		    ->get();

        $Trade = TradeOrder::create([
            'type'       => 1,
            'asset_type' => $asset_type,
            'member_id'  => $member_id,
            'quantity'   => $amount,
            'amount'     => $amount,
            'price'      => $price,
            'status'     => 0
        ]);
        $trade_id=$Trade->id;

        if(!empty($orders->toArray())){
        	// 剩余买入量
            $surplus_amount = $amount;
            foreach ($orders as $order) {
                if ($surplus_amount <= 0) break;
                $seller_id =  $order->member_id;
                $buyer_id =   $member_id;
                if ($order->amount <= $surplus_amount) {
                    $cur_amount = $order->amount; // 本次循环成交量
                    $surplus_amount -= $order->amount;
                    $order->status = 2;
                    $order->amount =0;
                    $order->save();
                } else {
                    $order->status = 1;
                    $order->amount = $order->amount - $surplus_amount;
                    $order->save();
                    $cur_amount = $surplus_amount;
                    $surplus_amount = 0;
                }
                // 取出买家卖家原持有资产
	            $buy_account_id = $this->accountService->getAccountId($buyer_id);
                $sale_account_id = $this->accountService->getAccountId($seller_id);
	            $buy_asset = Asset::fetchAssetData($buy_account_id, $asset_type);
	            $sale_asset = Asset::fetchAssetData($sale_account_id, $asset_type);
	            // 交易手续费
	            $poundage = $this->accountService->fetchPoundage($order->price, $cur_amount, $rate);
                // 买家向卖家转入现金资产
                $this->accountService->transferTo($buyer_id, $seller_id, Account::BALANCE, $order->price * $cur_amount);

                // 卖家向买家转入交易资产
                $this->accountService->transfer($seller_id, $member_id, $asset_type, $cur_amount,$order->id);
                $asset = AssetType::where('code',$asset_type)->first();
                $this->addRecharge2($asset,$buyer_id,$seller_id,$order->price,$cur_amount);
                $this->accountService->fee($seller_id, Account::BALANCE, $order->price * $cur_amount * $rate);
                 TradeLog::create([
                    'asset_type' => $asset_type,
                    'type'       => 1,
                    'buyer_id'   => $member_id,
                    'seller_id'  => $seller_id,
                    'amount'     => $cur_amount,
                    'price'      => $order->price,
                    'total'      => $cur_amount * $order->price
                ]);
                 // 计算最新资产价格
                 $AssetType = AssetType::where('code',$asset_type)->first();
                 $AssetType->market_value = $order->price;
                 $AssetType->save();

	            // 计算双方成本价
	            $this->accountService->buyCost($buy_asset['cost'], $buy_asset['amount'], $order->price, $cur_amount, $buy_account_id, $asset_type);
	            if($seller_id == $buyer_id) {
		            // 如果是自己交易，重取成本及持有，因买入会有影响
		            $sale_asset = Asset::fetchAssetData($sale_account_id, $asset_type);
		            $sale_asset['amount'] = $buy_asset['amount'] + $cur_amount;
	            }
	            $this->accountService->saleCost($sale_asset['cost'], $sale_asset['amount'], $order->price, $cur_amount, $sale_account_id, $asset_type, $seller_id, $poundage);
            }
            $bAccount_id = $this->accountService->getAccountId($member_id);
            //如果有剩余
            $Trade = TradeOrder::find($trade_id);
            if($surplus_amount >= 0){
                $Trade->amount = $surplus_amount;
                if($surplus_amount == 0){
                    $Trade->status = 2;
                    $Trade->save();
                    return ['code'=>200,'data'=>'委托买入成功'];
                }else{
                    $this->accountService->addFreeze($bAccount_id,Account::BALANCE,$surplus_amount*$price,1,$trade_id);
                    $Trade->status = 1;
                    $this->frozenBalance($member_id,$surplus_amount*$price);
                    $Trade->save();
                    return ['code'=>200,'data'=>'委托部分成交'];
                }
            }

        }else{
            $bAccount_id = $this->accountService->getAccountId($member_id);
            $this->accountService->addFreeze($bAccount_id,Account::BALANCE,$amount*$price,1,$trade_id);
            $this->frozenBalance($member_id,$amount*$price);
            return ['code'=>200,'data'=>'挂单委托成功'];
        }
    }

    /**
     *   撤销一个委托单
     *   $user_id   用户id
     *   $order_id  订单id
     */
    function cancelOrder($order_id)
    {
        DB::beginTransaction();
        $order = TradeOrder::findOrFail($order_id);
        if ($order->amount <= 0) return -1;
        $order->status = 3;
        $order->save();
        $this->accountService->revoked($order_id);
        DB::commit();
    }

    public function addRecharge($asset,$buyer_id,$seller_id,$price,$amount){
        $bContent = '交易扣除冻结余额'.$price * $amount.'元,增加'.$asset->name.$amount.'个';
        $this->financeService->adminRecharge($buyer_id,$asset->code,6, -1*$price * $amount,$amount,$bContent);
        $sContent = '交易扣除'.$asset->name.$amount.'个,增加'.$price * $amount.'元';
        $this->financeService->adminRecharge($seller_id,$asset->code,6, $price * $amount,-1*$amount,$sContent);
    }
    public function addRecharge2($asset,$buyer_id,$seller_id,$price,$amount){
        $bContent = '交易扣除余额'.$price * $amount.'元,增加'.$asset->name.$amount.'个';
        $this->financeService->adminRecharge($buyer_id,$asset->code,6, -1*$price * $amount,$amount,$bContent);
        $sContent = '交易扣除冻结'.$asset->name.$amount.'个,增加'.$price * $amount.'元';
        $this->financeService->adminRecharge($seller_id,$asset->code,6, $price * $amount,-1*$amount,$sContent);
    }

    public function frozenBalance($member_id,$price){
        $content = '交易挂单冻结余额'.$price.'元';
        $this->financeService->adminRecharge($member_id,Account::BALANCE,6, -1*$price,0,$content);
    }

    public function frozenAssets($member_id,$asset,$amount){
        $content = '交易挂单冻结'.$asset->name.$amount.'个';
        $this->financeService->adminRecharge($member_id,$asset->code,6,0,-1*$amount,$content);
    }

    public function FalseData($time, $type)
    {
        ////最大值 最小值 开盘 收盘
        // 交易量 交易额
        $init = $this->mockData;
        if($type == 1) {
            $new_data = [];
            if(isset($init[$time])) {
                foreach ($init[$time] as $item) {
                    $new_data[] = round($item/1454, 2);
                }
                array_unshift($new_data, $time);
            } else {
                $new_data = [$time, 0, 0 ,0, 0];
            }
            return $new_data;
        } else {
            if(isset($init[$time])) {
                return [$init[$time][0], intval($init[$time][1])];
            } else {
                return [0,0];
            }
        }
    }

    /**
     * k 线图天数据
     * @param $asset_type
     * @param string $begin_date
     * @param string $end_date
     * @return array
     */
    protected function kChartDataDay($asset_type, $begin_date = '', $end_date = '')
    {
        $carbon = new Carbon();
        $oneDayTime = 24 * 60 * 60;
        $price = 1.5;
        $date = $data = $list = $params = [];
        if (!$begin_date) {
            $day_num = 53;
            $begin_date = $carbon->subDays($day_num)->format('Y-m-d');
        } else {
            $day_num = intval((strtotime($end_date) - strtotime($begin_date)) / $oneDayTime);
        }
        if (!$end_date) {
            $end_date = $carbon::today()->format('Y-m-d');
        }
        for ($i = 1; $i <= $day_num; $i++) {
            $d = date('Y-m-d', strtotime($begin_date) + $i * $oneDayTime);
            if (in_array(date('w', strtotime($d)), [0, 6])) {
                continue;
            }
            $date[] = $d;
        }
        //最大值 最小值 开盘 收盘 交易量 交易额
        $_list = DB::table('trade_logs')
            ->select('price', 'amount', DB::raw('date(created_at) as created_date'))
            ->whereBetween('created_at', [$begin_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('asset_type', $asset_type)
            ->get();
        if ($_list) {
            foreach ($_list as $item) {
                $data[$item->created_date][] = (array)$item;
            }
        }
        foreach ($date as $k => $item) {
            if (array_key_exists($item, $data)) {
                $current = $data[$item];
                $_current = $current;
                $_change = array_column($current, 'price');
                $_first = array_pop($_current);
                $_last = array_shift($_current);
                $params[] = [
                    $item,
                    (float)$_first['price'],
                    $_last['price'] == null ? (float)$_first['price'] : (float)$_last['price'],
                    is_array($_change) && !empty($_change) ? (float)min($_change) : 0,
                    is_array($_change) && !empty($_change) ? (float)max($_change) : 0
                ];
                $moneys = array_column($current, 'total');
                $amounts = array_column($current, 'amount');
                $_params[$item] = [
                    round(array_sum($moneys), 2),
                    intval(array_sum($amounts))
                ];
            } else {
                $params[] = [$item, $price, $price, $price, $price];
                $_params[$item] = [0, 0];
            }
        }
        $start = date('Y-m-d', strtotime('-30 days'));
        if(date('w') == 0) {
            $end = date('Y-m-d', strtotime('-2 days'));
        }
        if(date('w') == 6) {
            $end = date('Y-m-d', strtotime('-1 days'));
        }
        return compact('date', 'data', 'params', '_params','start', 'end');
    }

    /**
     * k 线图小时数据
     * @param $asset_type
     * @param string $date
     * @return array
     */
    protected function kChartDataHour($asset_type, $date='')
    {
        if(!$date) {
            $date = date('Y-m-d');
        }
        $where = [
            ['price', '>', 0],
            ['asset_type', '=', $asset_type],
            ['type', '=', 1],
        ];
        $today_data = DB::table('trade_logs')
            ->where($where)
            ->select('price', 'created_at', 'amount', 'total', 'type')
            ->whereDate('created_at', '=', date('Y-m-d'))
            ->get()
            ->toArray();
        $date = [];
        $list = [];
        $params = [];
        $_params = [];
        $trade_set = TradeSet::where('asset_type', $asset_type)->select('start','end','rate','limit','trade_start')->first()->toarray();
        if(!$trade_set['start']|| !$trade_set['end']) {
            return ['date'=>[], 'params'=>[]];
        }
        $total = (strtotime(date('Y-m-d').' '.$trade_set['end']) - strtotime(date('Y-m-d').' '.$trade_set['start']))/(60);
        $start = date('Y-m-d').' '.$trade_set['start'];
        $end = date('Y-m-d').' '.$trade_set['end'];
        for ($i = 0; $i < $total; $i++) {
            $date[] = date('h:i', strtotime($i.' minutes', strtotime($start)));
        }
        if($today_data) {
            foreach ($today_data as $today_datum) {
                $format = date('h:i', strtotime($today_datum->created_at));
                $list[$format][] = (array)$today_datum;
            }
        }
        foreach ($date as $item) {
            if(array_key_exists($item, $list)) {
                $current = $list[$item];
                $_current = $current;
                $__current = $current;
                $_change = array_column($current, 'price');
                $_first = array_pop($_current);
                $_last = array_shift($__current);
                $params[] = [
                    $item,
                    (float)$_first['price'],
                    $_last['price'] == null ? (float)$_first['price'] : (float)$_last['price'],
                    is_array($_change) && !empty($_change) ? (float)min($_change) : 0,
                    is_array($_change) && !empty($_change) ? (float) max($_change) : 0
                ];
                $moneys = array_column($current, 'total');
                $amounts = array_column($current, 'amount');
                $_params[$item] = [
                    round(array_sum($moneys), 2),
                    intval(array_sum($amounts))
                ];
            } else {
                $params[] = [$item, 0, 0, 0, 0];
                $_params[$item] = [0, 0];
            }
        }
        return compact('date', 'data', 'params', '_params');
    }

    /**
     * @param $asset_type
     * @param int $type day 天, hour 小时
     * @return mixed
     */
    public function kChart($asset_type, $type =1){
        $call_func = 'kChartDataDay';
        if($type == 1) {
            $call_func = 'kChartDataDay';
        } elseif($type == 2) {
            $call_func = 'kChartDataHour';
        }

        return call_user_func([$this, $call_func],$asset_type);
    }

    /**
     * 获取今日之前的收盘价格
     * @param $asset_type
     * @return int
     */
    public  function lastPrice($asset_type){
        $today = Carbon::today();
        // 最后一条交易
        $trade_log = TradeLog::where('asset_type',$asset_type)->where('created_at','<',$today)->orderBy('id','desc')->first();

        if (empty($trade_log)) {
	        // 如果没有上一天的交易，取当天的第一笔，再没有取assets.market_value
	        $project = Project::where('asset_code', $asset_type)->first();
	        return $project->price;
        }

        return $trade_log['price'];

    }


    function fillTrade($count) {
        $buy_fill = [];
        for ($i = 0; $i < $count; $i++) {
            $buy_fill[] = ["price" => '-', 'amount' => '-'];
        }

        return $buy_fill;
    }

    function trades($code) {
        $_buy_trades = TradeOrder::select(\DB::raw('price, sum(amount) as amount'))
            ->where('asset_type', $code)
            ->where('type', 1)
            ->where(function($query){
                $query->where('status', 0)
                    ->orWhere('status', 1);
            })->orderBy('price', 'desc')
            ->orderBy('created_at', 'asc')
            ->groupBy('price')
            ->limit(5)
            ->get();

        $buy_trades = $this->fillTrade(5 - $_buy_trades->count());
        $buy_trades = collect(array_merge($_buy_trades->toArray(), $buy_trades));

        $sale_trades = TradeOrder::select(\DB::raw('price, sum(amount) as amount'))
            ->where('asset_type', $code)
            ->where('type', 2)
            ->where(function($query){
                $query->where('status', 0)
                    ->orWhere('status', 1);
            })->orderBy('price', 'asc')
            ->orderBy('created_at', 'asc')
            ->groupBy('price')
            ->limit(5)
            ->get()
            ->reverse();
        
        $_sale_trades = $this->fillTrade(5-$sale_trades->count());
        $sale_trades = collect(array_merge($_sale_trades, $sale_trades->toArray()));
        return [$buy_trades->reverse(), $sale_trades->reverse()];
    }
}