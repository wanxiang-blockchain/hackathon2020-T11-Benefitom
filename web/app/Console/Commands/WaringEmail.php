<?php

namespace App\Console\Commands;

use App\Utils\EmailUtil;
use App\Utils\RedisKeys;
use App\Utils\RedisUtil;
use Illuminate\Console\Command;

class WaringEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waring:email';

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
        while ($waring = RedisUtil::rpop(RedisKeys::WARING_EMAIL_LIST)) {
            EmailUtil::send('报警', $waring, $waring);
        }
    }
}
