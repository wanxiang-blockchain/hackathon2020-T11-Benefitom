<?php

namespace App\Console\Commands;

use App\Exceptions\TradeException;
use App\Model\Account;
use App\Model\Artbc\BtConfig;
use App\Model\Btshop\BlockAsset;
use App\Model\Btshop\BlockAssetLog;
use App\Model\Btshop\BlockAssetType;
use App\Model\Finance;
use App\Service\AccountService;
use App\Service\FinanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArttbcToCash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artbc:tocash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'exchange arttbc to artbcs and cny';

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
        $models = BlockAsset::where('code', BlockAssetType::CODE_ARTTBC)
            ->where('balance', '>', 0)
            ->get();
        $accountService = new AccountService();
        $config = BtConfig::fetchOne();
        foreach ($models as $model){
            DB::beginTransaction();

            // 扣除 arttbc
            BlockAssetLog::record($model->member_id, BlockAssetType::CODE_ARTTBC, -1 * $model->balance,
                BlockAssetLog::TYPE_TO_RMB_ARTBCS, '兑换为CNY+ARTBCS' . -1 * $model->balance);
            $toartbcs = intval($model->balance/2);
            // 添加 artbcs
            BlockAssetLog::record($model->member_id, BlockAssetType::CODE_ARTBCS, $toartbcs,
                BlockAssetLog::TYPE_FROM_ARTTBC, '兑换自ARTTBC' . $toartbcs);
            // 添加现金
            $tormb = $model->balance - $toartbcs;
            $account_id = $accountService->getAccountId($model->member_id);
            $accountService->addAsset($account_id, Account::BALANCE, $config->price * $tormb, '');
            if(!FinanceService::record($model->member_id, Account::BALANCE, Finance::WALLET_ARTBCS_EXCHANGE,
                $config->price * $tormb, 0, 'ARTTBC 兑现 ' . $config->price * $tormb.'元')) {
                throw new TradeException('兑现失败');
            }
            DB::commit();
        }
    }
}
