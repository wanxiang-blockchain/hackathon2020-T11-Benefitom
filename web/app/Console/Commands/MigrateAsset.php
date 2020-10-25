<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Exceptions\TradeException;
use App\Model\Account;
use App\Model\Btshop\BlockAsset;
use App\Model\Btshop\BlockAssetLog;
use App\Model\Btshop\BlockAssetType;
use App\Model\Finance;
use App\Model\FinanceType;
use App\Model\Member;
use App\Service\AccountService;
use App\Service\FinanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateAsset extends Command
{
    use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:asset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'migrate cash ARTTBC ARTBCS to ArTBC';

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
        $offset = 0;
        $members = Member::where('is_lock', 0)->offset($offset)->limit(1000)->get();
        while(count($members) > 0){
            foreach ($members as $member){
                $this->logmsg('migrate ' . $member->phone . ' ' . $member->id);
                $this->migrateCash($member);
                $this->migrateARTTBC($member);
                $this->migrateARTBCS($member);
            }
            $offset += 1000;
            $members = Member::where('is_lock', 0)->offset($offset)->limit(1000)->get();
        }
    }

    public function migrateCash($member)
    {
        \DB::beginTransaction();
        $accountService = new AccountService();
        $account_id = $accountService->getAccountId($member->id);
        $balance = $accountService->balance($member->id);
        $this->logmsg('cash balance:' . $balance);
        if ($balance > 0){
            $codeAmount = round($balance / 3, 2);
            $accountService->addAsset($account_id, Account::BALANCE, -1 * $balance, '');
            if(!FinanceService::record($member->id, Account::BALANCE, Finance::WALLET_SALE_COST, -1 * $balance, 0,
                '现金兑换为ArTBC,金额:'.$balance.'元')) {
                throw new TradeException('扣款失败');
            }
            BlockAssetLog::record($member->id, BlockAssetType::CODE_ARTBC, $codeAmount,
                BlockAssetLog::TYPE_FROM_CASH, '兑换自现金余额' . $codeAmount);
        }
        DB::commit();
    }

    public function migrateARTTBC($member)
    {
        DB::beginTransaction();
        $balance = BlockAsset::codeBalance($member->id, BlockAssetType::CODE_ARTTBC);
        $this->logmsg('arttbc balance: ' . $balance);
        if ($balance > 0){
            BlockAssetLog::record($member->id, BlockAssetType::CODE_ARTTBC, -1 * $balance,
                BlockAssetLog::TYPE_TO_ARTBC, '兑换为ArTBC' . $balance);
            BlockAssetLog::record($member->id, BlockAssetType::CODE_ARTBC,  $balance,
                BlockAssetLog::TYPE_FROM_ARTTBC, '兑换自ARTTBC' . $balance);
        }
        DB::commit();
    }

    public function migrateARTBCS($member)
    {
        DB::beginTransaction();
        $balance = BlockAsset::codeBalance($member->id, BlockAssetType::CODE_ARTBCS);
        $this->logmsg('artbcs balance: ' . $balance);
        if ($balance > 0){
            BlockAssetLog::record($member->id, BlockAssetType::CODE_ARTBCS, -1 * $balance,
                BlockAssetLog::TYPE_TO_ARTBC, '兑换为ArTBC' . $balance);
            BlockAssetLog::record($member->id, BlockAssetType::CODE_ARTBC,  $balance,
                BlockAssetLog::TYPE_FROM_ARTBCS, '兑换自ARTBCS' . $balance);
        }
        DB::commit();
    }

}
