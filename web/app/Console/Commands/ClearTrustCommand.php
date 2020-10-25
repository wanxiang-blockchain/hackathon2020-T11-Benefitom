<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\TradeService;
use App\Model\TradeOrder;
use Carbon\Carbon;

class ClearTrustCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trade:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'clear trust order';

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
	public function handle(TradeService $tradeService)
	{
		$trade_id = TradeOrder::where(function ($query) {
			$query->where('status', 0)
				->orwhere('status', 1);
		})->where('created_at','<',Carbon::now())->get()->pluck('id')->toarray();
		if(!empty($trade_id)){
			for($j = 0; $j < 3; $j++) {
				try{
					for($i=0;$i<count($trade_id);$i++){
						$tradeService->cancelOrder($trade_id[$i]);
					}
					echo '成功' . PHP_EOL;
				}catch (\Exception $e){
					echo $e->getTraceAsString();
				}
			}
		}

	}
}
