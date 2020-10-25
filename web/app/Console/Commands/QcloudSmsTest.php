<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Utils\QcloudSms;

class QcloudSmsTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qcloud:sms {phone}';

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
       QcloudSms::send('86', $this->argument('phone'), '111111');
    }
}
