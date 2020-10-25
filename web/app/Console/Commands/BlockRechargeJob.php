<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Exceptions\TradeException;
use App\Model\Artbc\Action;
use App\Model\Btshop\BlockAssetLog;
use App\Model\Btshop\BlockAssetType;
use App\Model\Btshop\BlockRechargeLog;
use App\Model\Btshop\BtshopOrder;
use App\Utils\BtshopUtil;
use App\Utils\EosScanUtil;
use App\Utils\EthScanUtil;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class BlockRechargeJob extends Command
{
    use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'block:recharge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'confirm block coin recharge with order number';

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
         *      完成： 充值
         *      超时或失败：修改相应订单状态
         */
        while (1) {
            $order_num = Redis::rpop(BtshopUtil::BLOCK_RECHARGE_TX_KEY);
            if (!$order_num) {
                sleep(1);
                continue;
            }
            $this->logmsg('get order_num:' . $order_num);
            try {
                DB::beginTransaction();
                try{
                    $recharge = BlockRechargeLog::fetchByOrderNum($order_num);
                    if (!$recharge) {
                        throw new TradeException($order_num . ' does not exists');
                    }
                    if ($recharge->stat !== BlockRechargeLog::STAT_ING) {
                        throw new TradeException('wrong stat ' . $recharge->stat . ' of ' . $order_num);
                    }
                    if ($recharge->code === BlockAssetType::CODE_ARTBC) {
                        // eth ArTBC
                        $trasaction = EthScanUtil::fetchTransatcion($recharge->tx);
                        if (!$trasaction) {
                            // 交易未取到，判断是否超时
                            if (time() >= (3600 + strtotime($recharge->created_at))) {
                                $this->logmsg('order_num ' . $order_num . ' transaction timeout');
                                $recharge->stat = BlockRechargeLog::STAT_TIMEOUT;
                                $recharge->save();
                            } else {
//                                $this->logmsg('order_num ' . $order_num . ' transaction not finish');
                                Redis::lpush(BtshopUtil::BLOCK_RECHARGE_TX_KEY, $order_num);
                            }
                            DB::commit();
                            continue;
                        }
                        if ($trasaction->name !== 'transfer'
                            || $trasaction->inputs[0] !== '6a68f55504809fa3efd140d9de4e070d275839b0') {
                            $this->logmsg('order_num ' . $order_num . ' transaction error receiver');
                            $recharge->stat = BlockRechargeLog::STAT_UNEXCEPT_PAY;
                            $recharge->save();
                            DB::commit();
                            continue;
                        }
                        if ($trasaction->inputs[1] < $recharge->amount) {
                            $this->logmsg('order_num ' . $order_num . ' payed ' . $trasaction->inputs[1] . ' less than ' . $recharge->amount);
                            $recharge->stat = BlockRechargeLog::STAT_UNEXCEPT_PAY;
                            $recharge->save();
                            DB::commit();
                            continue;
                        }
                        $receipt = EthScanUtil::getTransactionReceipt($recharge->tx);
                        if ($receipt['status'] !== '0x1'){
                            $receipt = EthScanUtil::getTransactionReceipt($recharge->tx);
                            if ($receipt['status'] !== '0x1'){
                                $receipt = EthScanUtil::getTransactionReceipt($recharge->tx);
                                if ($receipt['status'] !== '0x1') {
                                    $this->logmsg('order_num ' . $order_num . ' transaction failed');
                                    $recharge->stat = BlockRechargeLog::STAT_FAILED;
                                    $recharge->save();
                                    DB::commit();
                                    continue;
                                }
                            }
                        }
                        $this->logmsg('order_num ' . $order_num . ' transaction done');
                        $recharge->stat = BtshopOrder::STAT_DONE;
                        $recharge->save();
                        BlockAssetLog::record($recharge->member_id, $recharge->code, $recharge->amount, BlockAssetLog::TYPE_RECHARGE, '充值ArTBC ' . $recharge->amount);
                        DB::commit();
                        continue;
                    } else {
                        // cca ARTTBC
                        if (empty($recharge->txdata)){
                            $this->logmsg('order_num ' . $order_num . ' empty txdata');
                            $recharge->stat = BlockRechargeLog::STAT_FAILED;
                            $recharge->save();
                            DB::commit();
                            continue;
                        }
//                        $block = EosScanUtil::getBlock($recharge->tx);
                        $txdata = json_decode($recharge->txdata, true);
                        if (empty($txdata) || empty($txdata['transaction_id'])){
                            $this->logmsg('order_num ' . $order_num . ' empty txdata');
                            $recharge->stat = BlockRechargeLog::STAT_FAILED;
                            $recharge->save();
                            DB::commit();
                            continue;
                        }
                        $action = Action::fetchByTx($txdata['transaction_id']);
                        if (empty($action)){
                            // 交易未取到，判断是否超时
                            if (time() >= (86400 + strtotime($recharge->created_at))) {
                                $this->logmsg('order_num ' . $order_num . ' transaction timeout');
                                $recharge->stat = BlockRechargeLog::STAT_TIMEOUT;
                                $recharge->save();
                            } else {
//                                $this->logmsg('order_num ' . $order_num . ' transaction not finish');
                                Redis::lpush(BtshopUtil::BLOCK_RECHARGE_TX_KEY, $order_num);
                            }
                            DB::commit();
                            continue;
                        }
                        Log::debug('block ' . $recharge->tx, $action->toArray());
                        // 检测交易状态
                        if ($action->status !== 'executed'){
                            $this->logmsg('order_num ' . $order_num . ' transaction failed');
                            $recharge->stat = BlockRechargeLog::STAT_FAILED;
                            $recharge->save();
                            DB::commit();
                            continue;
                        }

                        if ($action['account'] !== 'shaodetang12' || $action['name'] !== 'transfer'){
                            $this->logmsg('order_num ' . $order_num . ' get unexcepted transaction');
                            $recharge->stat = BlockRechargeLog::STAT_UNEXCEPT_PAY;
                            $recharge->save();
                            DB::commit();
                            continue;
                        }

                        if ($action['from'] !== $recharge->account){
                            $this->logmsg('order_num ' . $order_num . ' get unexcepted from account');
                            $recharge->stat = BlockRechargeLog::STAT_UNEXCEPT_PAY;
                            $recharge->save();
                            DB::commit();
                            continue;
                        }
                        if ($action['to'] !== 'arttbcrevert'){
                            $this->logmsg('order_num ' . $order_num . ' get unexcepted to account');
                            $recharge->stat = BlockRechargeLog::STAT_UNEXCEPT_PAY;
                            $recharge->save();
                            DB::commit();
                            continue;
                        }
                        if (substr($action['quantity'], -7) !== ' ARTTBC'){
                            $this->logmsg('order_num ' . $order_num . ' get unexcepted quantity');
                            $recharge->stat = BlockRechargeLog::STAT_UNEXCEPT_PAY;
                            $recharge->save();
                            DB::commit();
                            continue;
                        }
                        if(floatval($action['quantity']) < floatval($recharge->amount)){
                            $this->logmsg('order_num ' . $order_num . ' get not enough quantity');
                            $recharge->stat = BlockRechargeLog::STAT_UNEXCEPT_PAY;
                            $recharge->save();
                            DB::commit();
                            continue;
                        }
                        $this->logmsg('order_num ' . $order_num . ' transaction done');
                        $recharge->stat = BtshopOrder::STAT_DONE;
                        $recharge->save();
                        BlockAssetLog::record($recharge->member_id, $recharge->code, $recharge->amount, BlockAssetLog::TYPE_RECHARGE, '充值ARTTBC ' . $recharge->amount);
                        DB::commit();
                        continue;
                    }
                }catch (TradeException $e){
                    $this->logmsg($e->getMessage());
                    DB::rollBack();
                    continue;
                }
            } catch (\Exception $e) {
                Redis::lpush(BtshopUtil::BLOCK_RECHARGE_TX_KEY, $order_num);
                $this->logmsg($e->getMessage());
                \Log::error($e->getTraceAsString());
            }
        }

    }
}
