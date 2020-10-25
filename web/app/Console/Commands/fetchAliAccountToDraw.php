<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Btshop\Aliaccount;
use App\Model\Btshop\AlipayDraw;
use Illuminate\Console\Command;

class fetchAliAccountToDraw extends Command
{

    use LogTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:aliaccount';

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
        return 404;
        //
        $models = AlipayDraw::where('account', '408120905@qq.com')->get();
        foreach ($models as $model){
            $this->logmsg('fetch aliaccount of ' . $model->member_id);
            $aliaccount = Aliaccount::fetchModel($model->member_id);
            $this->logmsg('get account ' . $aliaccount->account);
            $model->account = $aliaccount ? $aliaccount->account : '';
            $model->save();
        }
    }
}
