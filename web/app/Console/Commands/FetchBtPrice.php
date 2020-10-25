<?php

namespace App\Console\Commands;

use App\Model\Artbc;
use App\Model\Artbc\BtConfig;
use Illuminate\Console\Command;

class FetchBtPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:btprice';

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
        //
        $config = BtConfig::fetchOne();
        $config->price = Artbc::giftRate();
        $config->save();
    }
}
