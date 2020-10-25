<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\ProjectOrderController;
use App\Model\Candle;
use App\Utils\ChartUtil;
use App\Utils\TradeUtil;
use Illuminate\Console\Command;

class KServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'k:server';

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

        $cert_file = "/lnmp/nginx/conf/conf.d/cert/trade.yigongpan.com/214174326560168.pem";
        $key_file = "/lnmp/nginx/conf/conf.d/cert/trade.yigongpan.com/214174326560168.key";

        if(env('APP_ENV') == 'prod') {
	        $cert_file = "/lnmp/nginx/conf/conf.d/cert/trade/214151818600168.pem";
	        $key_file = "/lnmp/nginx/conf/conf.d/cert/trade/214151818600168.key";
        }

        //
	    $ws = new \swoole_websocket_server('0.0.0.0', env('SOCKET_PORT', 9502), SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);

	    $ws->set([
		    'worker_num' => 4,
		    'ssl_cert_file' => $cert_file,
		    'ssl_key_file' => $key_file,
		]);

	    $ws->on('open', function ($ws, $request) {
	    	echo date('Y-m-d H:i:s') . " connected by client" . $request->fd . PHP_EOL;
	    });

	    $ws->on('message', function ($ws, $frame) {
//		    echo "get message {$frame->data} from  client" . $frame->fd . PHP_EOL;
	    	// 获取wstoken|asset_code|type
		    // type: 0日线 1分时线 5-60 分k线
	    	$data = $frame->data;
	    	if(empty($data)) {
	    		return 0;
		    }

		    $arr = explode(',', $data);
	    	$wstoken = $arr[0];

	    	if (strlen($wstoken) == 50) {
	    		$res = TradeUtil::wsDetail($wstoken);
			    $ws->push($frame->fd, json_encode($res));
			    return 0;
		    }

		    if(count($arr) < 3) {
			    return 0;
		    }

	    	$asset_type = $arr[1];
	    	$ktype = $arr[2];

	    	// todo 验证wstoken

	    	if(!Candle::rightType($ktype)) {
	    		return 0;
		    }

	    	if($ktype == Candle::TYPE_MIN) {
			    $res = ChartUtil::min($asset_type);
		    }else {
	    		$res = ChartUtil::k($asset_type, $ktype);
		    }

		    // 在此处
		    $ws->push($frame->fd, json_encode($res));

	    });

	    $ws->on('close', function ($ws, $fd) {
		    echo date('Y-m-d H:i:s') . " client-{$fd} is closed\n";
	    });

	    $ws->start();
    }
}
