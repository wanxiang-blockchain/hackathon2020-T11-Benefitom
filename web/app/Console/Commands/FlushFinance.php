<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Account;
use App\Model\Asset;
use App\Model\Finance;
use App\Model\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FlushFinance extends Command
{
	use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:flush';

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
    	$this->logstart();
        // 1、取出所有用户
	    $members = Member::all()->toArray();
	    foreach ($members as $member) {
	    	$this->logmsg('start to flush member: ' . $member['id'] . ' ' . $member['phone']);
	    	$finances = Finance::where('member_id', $member['id'])->orderBy('id')->get();
	    	$after_amount = 0;
	    	if(empty($finances)) {
	    	    continue;
		    }
		    foreach ($finances as $finance) {
			    if($finance->asset_type != Account::BALANCE && $finance->type == 1)  {
			    	// 管理员充值藏品，不变更余额；交易扣除冻结余额1220元,增加人生十二悟100个，不变更余额
				    $this->logmsg('asset_type:' . $finance->asset_type . ' type:' . $finance->type. ' 不变更余额');
				    $finance->after_amount = $after_amount;
			    } else if ($finance->asset_type != Account::BALANCE && $finance->type == 6 && $finance->balance < 0){
				    // 交易扣除冻结余额1220元,增加人生十二悟100个，不变更余额，因为冻结时候已经变化 过
				    $this->logmsg('asset_type:' . $finance->asset_type . ' type:' . $finance->type. ' 不变更余额');
				    $finance->after_amount = $after_amount;
			    } else {
				    $this->logmsg('finance:' . $finance->id . ' last_after_amount:' . $after_amount . ' balance:' . $finance->balance);
				    $after_amount = $finance->after_amount = $finance->balance + $after_amount;
				    $this->logmsg('finance:' . $finance->id . ' new_after_amount:' . $finance->after_amount);
			    }
	    		$finance->save();
		    }
		    // 看after_amount 与 balance 是否能对上
		    $asset = DB::select('select sum(amount) as s_amount from assets where account_id = (select id from accounts where member_id = ?) and asset_type = ? and is_lock = 0', [$member['id'], Account::BALANCE]);
	    	if(!empty($asset) && $after_amount != $asset[0]->s_amount) {
	    		$this->logmsg("Error: member:" . $member['id'] . " after_amount:" . $after_amount . ' asset_amount:' . $asset[0]->s_amount);
		    }

	    }
    }
}
