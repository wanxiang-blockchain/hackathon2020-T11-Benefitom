<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/23
 * Time: 上午10:05
 */

namespace App\Model\Tender;


use App\Model\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TenderWithdraw extends Model
{
	protected $fillable = [
		'member_id', 'auditor', 'amount', 'note', 'stat', 'card', 'name', 'bank'
	];

	const STAT_INIT = 0;
	const STAT_ACCEPT = 1;
	const STAT_REJECT = 2;

	public static function acceptedAmount($member_id)
	{
		 $sum = DB::select('select sum(amount) as sum from tender_withdraws where stat = ?', [self::STAT_ACCEPT]);
		 return empty($sum) ? 0 : intval($sum[0]->sum);
	}

	public function member()
	{
		return $this->hasOne(Member::class, 'id', 'member_id');
	}

}