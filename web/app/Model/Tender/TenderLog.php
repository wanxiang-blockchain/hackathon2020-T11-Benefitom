<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/17
 * Time: 下午4:46
 */

namespace App\Model\Tender;


use App\Model\Member;
use Illuminate\Database\Eloquent\Model;

class TenderLog extends Model
{

	protected $fillable = [
		'member_id', 'tender_id', 'price'
	];

	public function tender()
	{
		return $this->hasOne(Tender::class, 'id', 'tender_id');
	}

	public function member()
	{
		return $this->hasOne(Member::class, 'id', 'member_id');
	}

}