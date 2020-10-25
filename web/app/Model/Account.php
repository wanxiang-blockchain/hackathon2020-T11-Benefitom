<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Asset;

class Account extends Model
{
    protected $fillable = [
        'member_id', 'is_lock','trade_pwd'
    ];
    public function assets() {
        return $this->hasMany("App\Model\Asset");
    }
	public function member() {
    	return $this->hasOne(Member::class, 'id', 'member_id');
	}
    //现金余额
    const BALANCE = "T000000001";
}
