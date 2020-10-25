<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WithDraw extends Model
{
    //
    protected $fillable = [
        'member_id', 'money', 'payment', 'reason','reject_time','status', 'aliname', 'fee', 'real_money'
    ];
    public function member() {
        return $this->belongsTo("App\Model\Member", "member_id", "id");
    }

    public static function add($mid, $money, $payment, $aliname)
    {
    	$fee = $money * 0.0015;
    	$fee < 2 && $fee = 2;
    	$fee > 25 && $fee = 25;
	    return WithDraw::create([
		    'member_id'=>$mid,
		    'money'=>$money,
		    'payment'=>$payment,
		    'aliname'=>$aliname,
		    'status'=>1,
		    'fee' => $fee,
		    'real_money' => $money - $fee
	    ]);
    }
}
