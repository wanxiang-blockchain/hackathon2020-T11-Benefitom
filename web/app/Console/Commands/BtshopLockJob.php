<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Exceptions\TradeException;
use App\Model\Artbc\BtScoreUnlock;
use App\Model\Btshop\BtshopOrder;
use App\Utils\BtshopUtil;
use App\Utils\EthScanUtil;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class BtshopLockJob extends Command
{
    use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'btshop:lock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '根据订单号和支付号完成奖励积分锁仓';

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
         * 1、取出队列中order_num
         * 2、根据order_num取出订单并判断其状态
         * 3、根据交易id取出交易状态
         *      完成： 锁仓积分
         *      超时或失败：修改相应订单状态
         */
        while (1) {
            $order_num = Redis::rpop(BtshopUtil::ORDER_TX_QUEUE_KEY);
            if (!$order_num) {
                sleep(1);
                continue;
            }
            $this->logmsg('get order_num:' . $order_num);
            try{
                $btshopOrder = BtshopOrder::fetchModelByOrderNum($order_num);
                if (!$btshopOrder) {
                    $this->logmsg($order_num . ' does not exists');
                    continue;
                }
                if ($btshopOrder->stat !== BtshopOrder::STAT_TX) {
                    $this->logmsg('wrong stat ' . $btshopOrder->stat . ' of ' . $order_num);
                    continue;
                }
                if ($btshopOrder->paytype == 1) {
                    // eth ArTBC
                    $trasactionStatus = EthScanUtil::fetchTransatcionStatus($btshopOrder->tx);
                    if (!$trasactionStatus) {
                        // 交易未取到，判断是否超时
                        if (time() >= (86400 + strtotime($btshopOrder->created_at))){
                            $this->logmsg('order_num ' . $order_num . ' transaction timeout');
                            $btshopOrder->stat = BtshopOrder::STAT_TIMEOUTE;
                            $btshopOrder->save();
                        }else{
                            $this->logmsg('order_num ' . $order_num . ' transaction not finish');
                            Redis::lpush(BtshopUtil::ORDER_TX_QUEUE_KEY, $order_num);
                        }
                        continue;
                    }
                    if (!isset($trasactionStatus['result']['isError']) || $trasactionStatus['result']['isError'] !== '0') {
                        $this->logmsg('order_num ' . $order_num . ' transaction failed');
                        $btshopOrder->stat = BtshopOrder::STAT_TIMEOUTE;
                        $btshopOrder->save();
                        continue;
                    }
                    $trasaction = EthScanUtil::fetchTransatcion($btshopOrder->tx);
                    \Log::debug($trasaction->name);
                    if ( $trasaction->name !== 'transfer'
                        || $trasaction->inputs[0] !== '6a68f55504809fa3efd140d9de4e070d275839b0') {
                        $this->logmsg('order_num ' . $order_num . ' transaction error receiver');
                        $btshopOrder->stat = BtshopOrder::STAT_TIMEOUTE;
                        $btshopOrder->save();
                        continue;
                    }
                    if ($trasaction->inputs[1] < $btshopOrder->price) {
                        $this->logmsg('order_num ' . $order_num . ' payed ' . $trasaction->inputs[1] . ' less than ' . $btshopOrder->price);
                        $btshopOrder->stat = BtshopOrder::STAT_TIMEOUTE;
                        $btshopOrder->save();
                        continue;
                    }
                    // 下放bt_unlock，考虑其邀请人
                    $this->logmsg('order_num ' . $order_num . ' transaction done');
                    $btshopOrder->stat = BtshopOrder::STAT_DONE;
                    $btshopOrder->save();
                    BtScoreUnlock::inviteAdd($btshopOrder->member_id, $btshopOrder->score * $btshopOrder->amount, $order_num);
                    continue;
                }else{
                    // cca ARTTBC
                    continue;
                }
            }catch (\Exception $e){
                Redis::lpush(BtshopUtil::ORDER_TX_QUEUE_KEY, $order_num);
                $this->logmsg($e->getMessage());
                \Log::error($e->getTraceAsString());
            }

        }
    }
}
