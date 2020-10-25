<?php

namespace App\Console\Commands;

use App\Model\Artbc\WalletInvite;
use App\Model\Member;
use Illuminate\Console\Command;

class FlushWalletInvite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flush:walletInvite';

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
        /*
         * 1、select * from members where wallet_invite_id > 0;
         * do while parent exist insert wallet_invites
         */
        return 404;
        $models = Member::where('wallet_invite_member_id', '>', 0)->select(['id', 'wallet_invite_member_id'])->get();
        foreach ($models as $model) {
            $level = 1;
            $parent = Member::find($model->wallet_invite_member_id);
            while($parent) {
                WalletInvite::add($model->id, $parent->id, $level);
                if ($parent->wallet_invite_member_id > 0) {
                    $parent = Member::find($parent->wallet_invite_member_id);
                    $level++;
                } else {
                    $parent = null;
                }
            }
        }
    }
}
