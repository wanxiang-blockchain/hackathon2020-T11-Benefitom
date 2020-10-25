<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Asset;
use App\Service\AccountService;
use Illuminate\Console\Command;

class ClearLockedAsset extends Command
{
	use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:locked-asset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(AccountService $accountService)
    {
        //
	    return 404;
	    $trade_ids = Asset::where('order_id', '>', 0)
		    ->get();
	    $this->logmsg("order_ids: " . json_encode($trade_ids));
	    if(!empty($trade_ids )){
		    try{
		    	foreach ($trade_ids as $id){
		    		$this->logmsg("Deal order_id {$id['order_id']}");
				    $accountService->revoked($id['order_id']);
			    }
			    $this->logmsg("done");
		    }catch (\Exception $e){
			    $this->logmsg($e->getTraceAsString());
		    }
	    }

    }
}
