<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Artbc\WalletInvite;
use Illuminate\Console\Command;

class CheckInvite extends Command
{
    use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:invite';

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
        $id = 0;
        do{
            $models = WalletInvite::where('id', '>', $id)
                ->orderBy('id')
                ->limit(1000)
                ->get();
            $id = $models[count($models)-1]['id'];
            foreach($models as $index => $model){
                $this->logmsg('start ' . $model->id);
                $exist = WalletInvite::where('pid', $model->member_id)
                    ->where('member_id', $model->pid)
                    ->exists();
                if ($exist){
                    $this->logmsg(json_encode($model->toArray()));
                }
            }
        }while(count($models) === 1000);
    }
}
