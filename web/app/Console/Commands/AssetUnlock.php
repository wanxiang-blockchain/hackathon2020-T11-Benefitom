<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssetUnlock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asset:unlock';

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

	    DB::beginTransaction();
	    //	     update `assets` set `is_lock` = ? where `account_id` = ? `asset_type` = ? and `is_lock` = ? and unlock_time < now()
	    DB::table('assets')
		    ->where('is_lock', 1)
		    ->where('unlock_time', '<', date('Y-m-d 00:00:00', time() + 86400))
		    ->update(['is_lock' => 0]);
	    DB::commit();
    }
}
