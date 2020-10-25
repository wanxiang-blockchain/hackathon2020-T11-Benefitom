<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Artbc\BtConfig;
use App\Model\Artbc\BtScore;
use App\Model\Artbc\BtScoreLog;
use App\Model\Artbc\BtScoreUnlock;
use App\Model\Artbc\BtScoreUnlockLog;
use App\Utils\DateUtil;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UnlockBtScore extends Command
{
	use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unlock:btscore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'unlock btscore';

    /**
     * 本周释放比例
     * @var int
     */
    protected $percent;

    /**
     * @var 释放比例
     */
    protected $period;

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
         * 1、取出stat=1 btscore_unlocks
         * 2、计算解锁数据
         * 3、修改状态
         */
        $models = BtScoreUnlock::where('stat', 1)->get();
        $btconfig = BtConfig::fetchOne();
        $this->percent = $btconfig ? $btconfig->percent : 1;
        $this->period = $btconfig ? $btconfig->period : 1;
        foreach ($models as $model) {
			$this->unlock($model);
        }
    }

    public function unlock(BtScoreUnlock $model)
    {
    	DB::beginTransaction();
    	try{
		    $last_unlock_time = $model->last_unlock_time ? $model->last_unlock_time : strtotime(date('Ymd', strtotime($model->created_at)));
		    $seconds = 86400 * $this->period;
		    if (date('Ymd') >= date('Ymd', $last_unlock_time + $model->period * $seconds)) {
		        // 计算每次释放数量
			    $perAmount = round($model->amount * $this->percent / 100, 2);
			    // 判断是否足够释放
			    $leftAmount = $model->amount - $model->unlocked_amount;
			    if ($leftAmount < $perAmount){
				    $perAmount = $leftAmount;
			    }
			    $model->unlocked_amount += $perAmount;
			    // 添加btscore 并 写btscoreLog
			    BtScoreLog::add($model->member_id, $perAmount, BtScoreLog::TYPE_UNLOCK);
			    if ($model->unlocked_amount >= $model->amount) {
			    	$model->stat = BtScoreUnlock::STAT_DONE;
			    }
			    $model->last_unlock_time = time();
			    if(!$model->save()){
			        throw new \Exception('unlock ' . $model->id . ' failed');
			    }
			    BtScoreUnlockLog::create([
			        'bt_score_unlock_id' => $model->id,
                    'member_id' => $model->member_id,
                    'unlock_amount' => $perAmount
                ]);
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
