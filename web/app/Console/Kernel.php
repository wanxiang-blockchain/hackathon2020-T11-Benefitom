<?php

namespace App\Console;

use App\Console\Commands\AddSuperMember;
use App\Console\Commands\AddUp;
use App\Console\Commands\ArttbcToCash;
use App\Console\Commands\AssetUnlock;
use App\Console\Commands\AutoIncresePrice;
use App\Console\Commands\BlockRechargeJob;
use App\Console\Commands\BtshopLockJob;
use App\Console\Commands\Calculate;
use App\Console\Commands\CandleLine;
use App\Console\Commands\CheckBlockExchange;
use App\Console\Commands\CheckInvite;
use App\Console\Commands\ClearLockedAsset;
use App\Console\Commands\CreatUser;
use App\Console\Commands\EmailTest;
use App\Console\Commands\fetchAliAccountToDraw;
use App\Console\Commands\FetchBtPrice;
use App\Console\Commands\FetchDisVip;
use App\Console\Commands\FixScore;
use App\Console\Commands\FlushBlockPayData;
use App\Console\Commands\FlushFinance;
use App\Console\Commands\FlushSpid;
use App\Console\Commands\FlushWalletInvite;
use App\Console\Commands\InsertVips;
use App\Console\Commands\KServer;
use App\Console\Commands\MigrateAsset;
use App\Console\Commands\PushInvite;
use App\Console\Commands\QcloudSmsTest;
use App\Console\Commands\SignCreate;
use App\Console\Commands\SmsTest;
use App\Console\Commands\TelegramBot;
use App\Console\Commands\TenderDeal;
use App\Console\Commands\TenderWinner;
use App\Console\Commands\UnitTest;
use App\Console\Commands\UnlockArtbc;
use App\Console\Commands\UnlockBtScore;
use App\Console\Commands\VipPaytypeRmb;
use App\Console\Commands\VipPush;
use App\Console\Commands\WalletInviteAppend;
use App\Console\Commands\WaringEmail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\InitDb::class,
        Commands\ClearTrustCommand::class,
	    CreatUser::class,
	    CandleLine::class,
	    Calculate::class,
	    SignCreate::class,
	    FlushFinance::class,
	    FixScore::class,
	    UnitTest::class,
	    KServer::class,
	    TenderWinner::class,
	    TenderDeal::class,
	    ClearLockedAsset::class,
	    AssetUnlock::class,
	    AddUp::class,
	    EmailTest::class,
	    TelegramBot::class,
	    AutoIncresePrice::class,
	    UnlockArtbc::class,
        UnlockBtScore::class,
        FlushSpid::class,
        SmsTest::class,
        FlushWalletInvite::class,
        BtshopLockJob::class,
        BlockRechargeJob::class,
        FlushBlockPayData::class,
        FetchDisVip::class,
        FetchBtPrice::class,
        CheckInvite::class,
        PushInvite::class,
        fetchAliAccountToDraw::class,
        QcloudSmsTest::class,
        AddSuperMember::class,
        WalletInviteAppend::class,
        WaringEmail::class,
        InsertVips::class,
        VipPush::class,
        CheckBlockExchange::class,
        VipPaytypeRmb::class,
        ArttbcToCash::class,
        MigrateAsset::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('trade:clear')
                  ->dailyAt("23:00");
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
