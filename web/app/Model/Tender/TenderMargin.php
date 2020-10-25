<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/21
 * Time: 上午10:52
 */

namespace App\Model\Tender;


use App\Model\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderMargin extends Model
{
	use SoftDeletes;
	protected $table = 'tender_margin';
	protected $fillable = [
		'member_id', 'amount'
	];

	public static function exist($member_id)
	{
		return static::where(['member_id' => $member_id])->exists();
	}

	public function member()
	{
		return $this->hasOne(Member::class, 'id', 'member_id');
	}
}