<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WithdrawAudit extends Model
{
    //
	use HasMemberTrait;
	protected $fillable = ['member_id', 'amount', 'audit_id', 'reason', 'status'];

	public static function statMap()
	{
		return [
			0 => '待审核',
			1 => '已打款',
			2 => '已驳回'
		];
	}

	public static function statLabel($stat)
	{
		$map = static::statMap();
		return isset($map[$stat]) ? $map[$stat] : '其他';
	}

	public function account()
	{
		return $this->hasOne(Account::class, 'member_id', 'member_id');
	}
}
