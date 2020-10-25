<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Member;
use App\Service\MemberService;
use Illuminate\Console\Command;

class FlushSpid extends Command
{
    use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flush:spid';

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
        /**
         * 1、取出所有有上级者
         */
        $models = Member::where('wallet_invite_member_id', '>', 0)->get();
        foreach ($models as $model) {
            // 寻找spid 并修正
            $spid = $model->spid;
            $sparent = Member::find($spid);
            while ($sparent->wallet_invite_member_id > 0){
                $sparent = Member::find($sparent->wallet_invite_member_id);
            }
            if ($model->spid !== $sparent->id){
                $this->logmsg('id:' . $model->id . ' orgin_spid:' . $model->spid . ' newspid:' . $sparent->id);
            }
            $model->spid = $sparent->id;
            $model->save();
            // 修正 wallet_invites 表
            MemberService::walletInvite($model);
        }
    }
}
