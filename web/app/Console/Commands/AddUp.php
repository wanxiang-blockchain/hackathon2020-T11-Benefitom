<?php

namespace App\Console\Commands;

use App\Model\Candle;
use App\Model\Finance;
use App\Model\TradeLog;
use Illuminate\Console\Command;

class AddUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计交易数量';

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
     *
     * @return mixed
     */
    public function handle()
    {
        //
	    $trade_days = $this->trade_days();
	    $trade_amount = $this->trade_amount();
	    echo "总交易日：" . $trade_days. PHP_EOL;
	    echo "总交易量：" . $trade_amount . PHP_EOL;
	    echo "日平均交易量：" . round($trade_amount / $trade_days, 2) . PHP_EOL;
	    echo "交易佣金：" . $this->commission() . PHP_EOL;

    }

    // 交易日
    public function trade_days()
    {
        return Candle::where('type', 0)->where('asset_code', '017001')->count();
    }

    // 总交易量
	public function trade_amount()
	{
		return TradeLog::sum('amount');
	}

	// 交易佣金
	public function commission()
	{
		return Finance::where('type', 5)->sum('balance');
	}

}
