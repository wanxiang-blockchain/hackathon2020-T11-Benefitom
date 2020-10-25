<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Tender\Tender;
use App\Model\Tender\TenderLog;
use App\Model\Tender\TenderMsg;
use App\Utils\DateUtil;
use Illuminate\Console\Command;

class TenderDeal extends Command
{
	use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tender:deal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算拍品成交价';

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
	    $this->logstart();
	    $now = DateUtil::now();
	    \DB::beginTransaction();
	    try{

		    $models = Tender::where('tender_end', '<', $now)
			    ->where('deal_log_id', 0)
			    ->get();
		    if(empty($models)){
			    exit;
		    }
		    foreach ($models as $model) {
			    $this->logmsg('deal tender_id[' . $model->id . ']');
			    $tender_log = TenderLog::where('tender_id', $model->id)
				    ->orderByDesc('price')
				    ->orderBy('id')
				    ->first();
			    if (empty($tender_log)) {
				    $this->logmsg('no tender_log tender_id[' . $model->id . ']');
				    continue;
			    }
			    if ($tender_log->price < $model->starting_price) {
				    $this->logmsg('deal price small then string_price tender_id[' . $model->id . ']');
			    }
			    $model->deal_log_id = $tender_log->id;
			    $this->logmsg('deal tender_id[' . $model->id . '] deal_log_id[' . $tender_log->id . ']');
			    if(!$model->save()){
			        throw new \Exception('deal tender_id[' . $model->id .'] failed');
			    }
			    // 给成交用户发消息
			    TenderMsg::setTempTender($tender_log->member_id, $model->name, $tender_log->price, $tender_log->id);
		    }
		    \DB::commit();
	    }catch (\Exception $e){
	        \DB::rollBack();
	        $this->logmsg($e->getTraceAsString());
	    }
    }
}
