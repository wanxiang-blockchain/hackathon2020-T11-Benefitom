<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/16
 * Time: 上午11:35
 */

namespace App\Model\Tender;


use App\Model\Member;
use Illuminate\Database\Eloquent\Model;

class TenderWinner extends Model
{
	protected $table = 'tender_winner';

	protected $fillable = [
		'tender_id', 'member_id', 'bonus', 'winner_type'
	];

	public function phone()
	{
		return substr_replace($this->member->phone, '****', 3, 4);
	}

	public function member()
	{
		return $this->hasOne(Member::class, 'id', 'member_id');
	}

	public function tender()
	{
		return $this->hasOne(Tender::class, 'id', 'tender_id');
	}

}