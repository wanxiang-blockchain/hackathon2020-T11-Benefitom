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

class TenderGuess extends Model
{

	protected $table = 'tender_guess';

	protected $fillable = [
		'tender_id', 'tender_price', 'member_id', 'winner_type'
	];

	const PRICE_NONE = 0;
	const PRICE_WINNER = 1;
	const PRICE_FIRST = 2;

	public function member()
	{
		return $this->hasOne(Member::class, 'id', 'member_id');
	}

	public function tender()
	{
		return $this->hasOne(Tender::class, 'id', 'tender_id');
	}

	public static function winnerLabel($type)
	{
		switch ($type) {
			case 0:
				return '无奖';
			case 1:
				return '火眼金睛奖';
			case 2:
				return '金睛奖';
		}
	}

	public function winnerType()
	{
		return static::winnerLabel($this->winner_type);
	}

	public static function add($tender_id, $price, $member_id)
	{
		return TenderGuess::create([
			'tender_id' => $tender_id,
			'tender_price' => $price,
			'member_id' => $member_id,
			'winner_type' => 0,
		]);
	}

}