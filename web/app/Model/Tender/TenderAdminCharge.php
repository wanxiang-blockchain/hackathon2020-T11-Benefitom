<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/24
 * Time: 下午1:34
 */

namespace App\Model\Tender;


use App\Model\Member;
use Illuminate\Database\Eloquent\Model;

class TenderAdminCharge extends Model
{
	protected $table = 'tender_admin_charge';

	protected $fillable = [
		'add_admin', 'auditor', 'amount', 'note', 'member_id', 'type'
	];

	const STAT_INIT   = 0;
	const STAT_ACCEPT = 1;
	const STAT_REJECT = 2;

	public function member()
	{
		return $this->hasOne(Member::class, 'id', 'member_id');
	}

}