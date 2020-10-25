<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Artbc\WalletInvite;
use App\Model\Member;
use App\Service\MemberService;
use App\Utils\RedisKeys;
use App\Utils\RedisUtil;
use Illuminate\Console\Command;

class WalletInviteAppend extends Command
{
    use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invite:append';

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
        $member_id = RedisUtil::rpop(RedisKeys::WALLET_INVITE_FLUSH_LIST);
        while ($member_id){
            if ($member_id <= 0){
                return 404;
            }
            $this->logmsg('member_id:' . $member_id);
            $subMembers = WalletInvite::fetchSons($member_id);
            foreach($subMembers as $subMember) {
                $subMemberModel = Member::find($subMember->member_id);
                MemberService::walletInvite($subMemberModel);
            }
            $member_id = RedisUtil::rpop(RedisKeys::WALLET_INVITE_FLUSH_LIST);
        }
    }
}
