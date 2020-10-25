<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use Illuminate\Console\Command;
use App\Model\Project;

class AutoIncresePrice extends Command
{
	use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:increse';

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
    public function handle()
    {
        return 404;
        //
	    $model = Project::where('asset_code', '018001')->first();
	    if (!$model){
			$this->logmsg("no model");
	    	return "no model";
	    }

	    // 看看是否今天修改过
	    $now = date('Y-m-d 00:00:00');
	    if ($model->updated_at > $now){
		    $this->logmsg("it's updated today");
	    	return "it's updated today";
	    }



	    if ($model->price >= 1780){
	    	$this->logmsg("Enough");
	    	return "Enough";
	    }

	    $this->logmsg("origin project price is: " . $model->price);
	    $model->price += 10;
	    $model->save();
	    // asset_types
	    $model->assetType->market_value = $model->price;
	    $model->assetType->save();
	    $this->logmsg("upgrade project price to: " . $model->price);
	    return 'done';
    }
}
