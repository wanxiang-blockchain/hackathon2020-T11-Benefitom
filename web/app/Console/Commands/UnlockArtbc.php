<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Artbc\ArtbcUnlock;
use App\Model\ArtbcLog;
use App\Utils\DateUtil;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UnlockArtbc extends Command
{
	use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unlock:artbc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'unlock artbc';

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
        /**
         * 1、取出stat=1 artbc_unlocks
         * 2、计算解锁数据
         * 3、修改状态
         */
        $models = ArtbcUnlock::where('stat', 1)->where('start_unlock_day', '<=', DateUtil::today())->get();
        foreach ($models as $model) {
			$this->unlock($model);
        }
    }

    public function unlock(ArtbcUnlock $model)
    {
    	DB::beginTransaction();
    	try{
		    $last_unlock_day = $model->last_unlock_day ? $model->last_unlock_day : $model->created_at;
		    $last_unlock_day = strtotime($last_unlock_day);
		    if ((time() - $last_unlock_day) >= $model->unlock_period * 86400) {
			    $perAmount = round($model->amount / $model->unlock_times, 0);
			    $leftAmount = $model->amount - $model->unlocked_amount;
			    if ($leftAmount < $perAmount){
				    $perAmount = $leftAmount;
			    }
			    $model->unlocked_amount += $perAmount;
			    // 添加Artbc 并 写ArtbcLog
			    ArtbcLog::add($model->member_id, $perAmount, ArtbcLog::TYPE_UNLOCK);
			    if ($model->unlocked_amount == $model->amount) {
			    	$model->stat = ArtbcUnlock::STAT_DONE;
			    }
			    $model->last_unlock_day = DateUtil::now();
			    if(!$model->save()){
			        throw new \Exception('unlock ' . $model->id . ' failed');
			    }
			    $this->logmsg('unlock ' . $model->id . ' success');
			    DB::commit();
		    }
		    DB::rollBack();
	    }catch (\Exception $e) {
    		DB::rollBack();
    		$this->logmsg($e->getMessage());
			return false;
	    }
    }
}
