<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Exceptions\TradeException;
use App\Model\Artbc\BtScoreLog;
use App\Model\Artbc\BtScoreUnlock;
use App\Model\Artbc\BtScoreUnlockLog;
use App\Model\Btshop\BtshopOrder;
use App\Model\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FlushBlockPayData extends Command
{
    use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flush:block:pay';

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
        // created_at > '2019-01-03 10:19:00' and created_at < '2019-01-04 10:06:00'
        $start = '2019-01-03 10:19:00';
        $end = '2019-01-04 10:06:00';
        $models = BtshopOrder::where('created_at', '>', $start)
            ->where('created_at', '<', $end)
            ->whereIn('paytype', [1, 2])
            ->where('stat', 2)
            ->get();
        $this->logmsg('rows count' . count($models));
        foreach ($models as $model){
            $this->fix($model);
        }
    }
    public function fix($model){
        // 扣除锁仓积分
        DB::beginTransaction();
        try{
            $this->logmsg('member_id ' . $model->member_id . ' btshop_orders.id ' . $model->id);
            // 处理锁仓积分
            $this->unlockFlush($model);
            // 处理奖励积分
            $member = Member::find($model->member_id);
            $parent = Member::walletParent($member->wallet_invite_member_id);
            if ($parent) {
                // 上级直接奖励5%
                $amountPrized =  round($model->amount * $model->score * 0.025, 2);
                if ($model->paytype == 1){
                    BtScoreLog::add($parent->id,  -1 * round($amountPrized / 2, 2),BtScoreLog::TYPE_SYSTEM_REVERT);
                }else{
                    BtScoreLog::add($parent->id,  -1 * $amountPrized,BtScoreLog::TYPE_SYSTEM_REVERT);
                }
                $pParent = Member::walletParent($parent->wallet_invite_member_id);
                if ($pParent && Member::walletInviteSum($pParent->id) >= 3){
                    $amountPrized =  round($model->amount * $model->score * 0.01, 2);
                    if ($model->paytype == 1){
                        BtScoreLog::add($pParent->id,  -1 * round($amountPrized / 2, 2),BtScoreLog::TYPE_SYSTEM_REVERT);
                    }else{
                        BtScoreLog::add($pParent->id,  -1 * $amountPrized,BtScoreLog::TYPE_SYSTEM_REVERT);
                    }
                }
            }
            // 处理已释放积分
            if ($model->paytype == 1){
                $model->score = round($model->score / 2, 2);
            }else{
                $model->score = 0;
            }
            $model->save();
            DB::commit();
        }catch (TradeException $e){
            DB::rollBack();
            $this->logmsg($e->getMessage());
            $this->logmsg($e->getTraceAsString());
        }
    }

    public function unlockFlush($model)
    {
        $unlock = BtScoreUnlock::where('member_id', $model->member_id)
            ->where('created_at', '>=', $model->created_at)
            ->first();
        if (empty($unlock)){
            throw new TradeException($model->order_num. ' get non bt_score_unlock');
        }
        $unloked = $unlock->unlocked_amount;
        if ($model->paytype == 1){
            $unlock->amount = round($unlock->amount / 2, 2);
            $unlock->unlocked_amount =  round($unlock->unlocked_amount / 2, 2);
            $unlock->save();
            $this->logmsg('unloced amount from ' . $unloked . ' to ' . $unlock->unlocked_amount);
            if ($unloked > 0){
                BtScoreLog::add($model->member_id,  -1 * round($unloked/2, 2),BtScoreLog::TYPE_SYSTEM_REVERT);
            }
        }else{
            $unlock->amount = 0;
            $unlock->unlocked_amount = 0;
            $unlock->save();
            $this->logmsg('unloced amount from ' . $unloked . ' to ' . $unlock->unlocked_amount);
            if ($unloked > 0){
                BtScoreLog::add($model->member_id,  -1 * $unloked,BtScoreLog::TYPE_SYSTEM_REVERT);
            }
        }
    }
}
