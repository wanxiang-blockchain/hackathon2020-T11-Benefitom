<?php

namespace App\Console\Commands;

use App\Utils\SmsUtil;
use Illuminate\Console\Command;

class SmsTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {phone}';

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
        SmsUtil::send($this->argument('phone'), '【艺行派】您的验证码: 111111');
    }
}
