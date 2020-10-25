<?php

namespace App\Console\Commands;

use App\Helpers\SendSmsHelper;
use Illuminate\Console\Command;

class UnitTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unit:test {cmd} {params?}';

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
        $cmd = $this->argument('cmd');
        $params = $this->argument('params');
        $this->$cmd($params);
    }

	/**
	 * 发短信测试
	 * @desc sms
	 * @param $phone
	 */
    public function sms($phone)
    {
    	var_dump(SendSmsHelper::withdrawNotice($phone));
    }

    public function tx_isolation()
    {
    	$res = \DB::select("show variables like 'tx_isolation' ");
    	var_dump($res);
	    \DB::statement("set tx_isolation='SERIALIZABLE';");
	    $res = \DB::select("show variables like 'tx_isolation'");
	    var_dump($res);
	    \DB::statement("set tx_isolation='SERIALIZABLE';");
    }

}
