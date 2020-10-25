<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Btshop\BlockAssetExchangeLog;
use Illuminate\Console\Command;

class CheckBlockExchange extends Command
{
    use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'block:exchangecheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $amount;

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
        $id = 0;
        $this->amount = 0;
        $models = BlockAssetExchangeLog::where('id', '>', $id)->limit(100)->get();
        while (count($models) > 0) {
            foreach ($models as $model) {
                $this->check($model);
                $id = $model->id;
            }
            $models = BlockAssetExchangeLog::where('id', '>', $id)->limit(100)->get();
        }
        $this->logmsg('total: '. $this->amount);
    }

    public function check($model)
    {
        $time = strtotime($model->created_at) + 20;
        $next = BlockAssetExchangeLog::where('id', '>', $model->id)
            ->where('member_id', $model->member_id)
            ->where('created_at', '>', $model->created_at)
            ->where('created_at', '<', date('Y-m-d H:i:s', $time))
            ->first();
        if ($next && $next->amount === $model->amount){
            $this->amount += $model->amount;
            if (empty($model->member)){
                echo ($model->member_id . ' ' . $model->amount . ' ' . $model->created_at . ' ' . $next->created_at) . PHP_EOL;
            }else{
                echo ($model->member->phone . ' ' . $model->amount . ' ' . $model->member_id . ' ' . $model->created_at . ' ' . $next->created_at) . PHP_EOL;
            }
        }
    }
}
