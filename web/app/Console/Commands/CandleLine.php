<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CandleLine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'candle:min';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '启动分时线socket';

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
	    $server = new \swoole_websocket_server("127.0.0.1", 9501);

	    $server->on('open', function (\swoole_websocket_server $server, $request) {
		    echo "server: handshake success with fd{$request->fd}\n";
	    });

	    $server->on('WorkerStart', function (\swoole_websocket_server $server, $worker_id) {
		    $server->tick(1000, function ($id) use ($server) {
			    foreach($server->connections as $fd)
			    {
			    	// TODO 发送分时线信息
				    $server->push($fd, "hello：" . date('Y-m-d H:i:s'));
			    }
		    });
	    });

	    $server->on('message', function (\swoole_websocket_server $server, $frame) {
		    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
		    $server->push($frame->fd, "this is server");
	    });

	    $server->on('close', function ($ser, $fd) {
		    echo "client {$fd} closed\n";
	    });

	    $server->start();

    }
}
