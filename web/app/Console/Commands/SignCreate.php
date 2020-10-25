<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SignCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sign:create {data}';

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
	    parse_str($this->argument('data'), $data);
	    ksort($data);
	    $sign = md5(implode('|', $data) . 'Kadfnoi1)af!_*kdafd');
	    echo $sign;
    }
}
