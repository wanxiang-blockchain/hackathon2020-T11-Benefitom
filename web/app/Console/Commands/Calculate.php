<?php

namespace App\Console\Commands;

use App\Model\AssetType;
use App\Model\Candle;
use App\Model\TradeLog;
use App\Model\TradeSet;
use App\Service\CandleService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Calculate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'candle:calculate {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算k线图';

	private $_startTime;

	/**
	 * 数值类型
	 *  0:日k, 1:分时，5：5分k, 15, 30, 60
	 * @var array|string
	 */
    public $type;

    // 日k
    const TYPE_DAY = 0;
    // 分时
    const TYPE_MIN = 1;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @param type
     * @return mixed
     */
    public function handle()
    {
    	$this->logstart();
	    $this->type = $this->argument('type');
	    $type_arr = [0, 1, 5, 10, 15, 30, 60];
	    if (!in_array($this->type, $type_arr)) {
	    	return 'error type: type must in [0, 1, 5, 10, 15, 30, 60]';
	    }


	    return $this->k();
    }

    protected function logstart()
    {
    	$this->_startTime = time();
    }

	/**
	 * log日志
	 *
	 * @param $msg string 日志内容
	 */
	protected function logmsg($msg)
	{
		echo date('Y-m-d H:i:s')
			. ' pid[' . posix_getpid() . ']'
			. ' cost[' . (time() - $this->_startTime) . ']'
			. ' memory[' . memory_get_usage()/1024/1024 . 'M]'
			. ' message[' . $msg . ']' . PHP_EOL;
		return 'exit';
	}

	/**
	 * 计算某个资产的k线
	 * @desc countAssetCandle
	 */
    private function countAssetCandle($tradeSet)
    {
		// 1 取出时间段左开右闭 > start and <= end
	    $start = $this->startTime($tradeSet);
	    // 2、取出时间段右闭值
	    $end = $this->endTime($tradeSet, $start);

	    $this->logmsg("start : $start, end: $end");

	    $ts = Carbon::now();

	    while ($end <= date('Y-m-d H:i:s')) {

	    	// 如果不在交易时间内，取下一区间
		    if($this->type != Candle::TYPE_DAY && (date('H:i:s', strtotime($end)) > $tradeSet['end2'] || date('H:i:s', strtotime($start)) < $tradeSet['start'])) {
			    $start = $end;
			    $end = $this->endTime($tradeSet, $start);
		    	continue;
		    }

//		    $this->logmsg("select * from trade_logs where created_at > $start and created_at <= $end");

	    	// 取出时间段内所有交易
		    $trades = TradeLog::where('asset_type', $tradeSet['asset_type'])
			    ->where('created_at', '>', $start)
			    ->where('created_at', '<=', $end)
			    ->select('amount', 'price')
			    ->get()
		        ->all();

		    if(!$trades) {
		        $start = $end;
		        $end = $this->endTime($tradeSet, $start);
		        continue;
		    }

		    /**
		     */
		    Candle::create([
			    'asset_code' => $tradeSet['asset_type'],
			    'type' => $this->type,
			    'name' => $this->type == Calculate::TYPE_DAY ? date('Y-m-d', strtotime($start)) : $start,
			    'value' => $this->kValue($tradeSet, $start, $trades)
		    ]);
		    $start = $end;
		    $end = $this->endTime($tradeSet, $start);
	    }
    }

	/**
	//            x轴                              时间        均价     涨跌    成交
	{ 'name': '2017/09/09 14:11:11', 'value': ['2017/09/09', 'open, 'close', '100',  '+1%', '99'] },
	 * @desc minValue
	 */
    private function minValue($tradeSet, $start, $trades)
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
	    $lastPrice = $this->prevPrice($start, $tradeSet['asset_type']);
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

	    return json_encode([
	    	'name' => $name,
		    'value' => [ $name, $open, $close, $average_price, $increase, $amount]
	    ]);

    }

	/**
	 * 根据某一时间段内的交易数据计划出K线展示数据
	 * @desc kValue
	 * @param $trades
	 * @return json
	//  日期        开盘        收盘      涨跌     涨幅      最低      最高
	['2015/12/31','3570.47','3539.18','-33.69','-0.94%','3538.35','3580.6', '成交量'],
	 */
    private function kValue($tradeSet, $start, $trades)
    {
    	if($this->type == self::TYPE_MIN) {
    		return $this->minValue($tradeSet, $start, $trades);
	    }
    	/**
	     * 1. 求日期
	     */
    	$date = $start;
    	if($this->type == self::TYPE_DAY)
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
    	$lastPrice = $this->prevPrice($start, $tradeSet['asset_type']);
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
	    $volume = 0; // 成交额
	    foreach ($trades as $trade) {
	    	$trade['price'] < $low && $low = $trade['price'];
		    $trade['price'] > $high && $high = $trade['price'];
		    $amount += $trade['amount'];
		    $volume += ($trade['price'] * $trade['amount']);
	    }

	    return json_encode([$date, $open, $close, $increase_amount, $increase, $low, $high, $amount, $volume]);

    }

	/**
	 * 获取收一个区间的收盘价
	 * @desc prevPrice
	 * @param $time
	 * @param $asset_type
	 * @return int
	 */
    private function prevPrice($time, $asset_type)
    {
    	// 如果是分时要取昨收
    	if($this->type == self::TYPE_MIN) {
    		$time = date('Y-m-d', strtotime($time)) . ' 00:00:00';
	    }
    	$tradeLog = TradeLog::where('asset_type', $asset_type)
		    ->where('created_at', '<=', $time)
		    ->orderBy('id', 'desc')
		    ->select('price')
		    ->first();
    	return isset($tradeLog['price']) ? $tradeLog['price'] : 0;
    }

	/**
	 * 取右闭区间
	 * @desc candleEndTime
	 * @param $tradeSet
	 * @param $start
	 * @return false|string
	 */
    private function endTime($tradeSet, $start)
    {
    	switch ($this->type) {
	    	// 日k 取一天交易开结束时间内
		    case static::TYPE_DAY:
		    	// 计算下一天
		    	return date('Y-m-d', strtotime($start) + 86400);
		    default:
		    	return date('Y-m-d H:i:s', strtotime($start) + $this->type * 60);
	    };
    }

	/**
	 * @desc candleStartTime
	 * @param $code
	 *     资产编号
	 */
    private function startTime($tradeSet)
    {
    	// 1、取上一条计算完的时间
	    $candle = Candle::where('asset_code', $tradeSet['asset_type'])
		    ->where('type', $this->type)
		    ->orderBy('id', 'desc')->first();
	    if($candle) {
	    	// 取最后一个区间的下一个下一个区间段
		    if($this->type == self::TYPE_DAY) {
		    	return date('Y-m-d 00:00:00', strtotime($candle['name']) + 86400);
		    }

		    return date('Y-m-d H:i:s', strtotime($candle['name']) + $this->type * 60);

	    }

    	return $this->type == self::TYPE_DAY ? $tradeSet['trade_start'] : $tradeSet['trade_start'] . ' ' . $tradeSet['start'];
    }

    private function tradeSets()
    {
	    return TradeSet::where('trade_start', '<=', date('Y-m-d'))->get();
    }

	/**
	 * 计算k线图
	 * @desc k
	 */
    public function k()
    {
    	// 取出所有当前开启交易的资产
	    $tradeSets = $this->tradeSets();
    	foreach ($tradeSets as $tradeSet)
	    {
			$this->countAssetCandle($tradeSet);
	    }

    }
}
