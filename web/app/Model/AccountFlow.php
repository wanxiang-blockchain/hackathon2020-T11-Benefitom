<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class AccountFlow extends Model
{

    protected $fillable = ['member_id', 'recharge_money', 'real_money', 'total_balance', 'desc','created_at'];

    public function create_log($member_id,$recharge_money, $real_money, $desc = '')
    {
    	DB::beginTransaction();
	    try{
		    $log = $this->orderBy('id', 'desc')->first();
		    if ($log) {
			    $total_balance = $log->total_balance;
		    } else {
			    $total_balance = 0;
		    }
		    if( $this->create([
			    'member_id' => $member_id,
			    'recharge_money' => $recharge_money,
			    'real_money' => $real_money,
			    'total_balance' => $total_balance + $real_money,
			    'desc' => $desc
		    ]) ){
			    DB::commit();
			    return true;
		    }
		    DB::rollBack();
		    return false;
	    } catch (Exception $e) {
	    	DB::rollBack();
		    return false;
	    }
    }

    public function create_logs($member_id,$recharge_money, $real_money, $desc = '',$created_at)
    {
        $log = $this->orderBy('id', 'desc')->first();
        if ($log) {
            $total_balance = $log->total_balance;
        } else {
            $total_balance = 0;
        }
        return $this->create([
            'member_id' => $member_id,
            'recharge_money' => $recharge_money,
            'real_money' => $real_money,
            'total_balance' => $total_balance + $real_money,
            'desc' => $desc,
            'created_at'=> $created_at
        ]);
    }
}
