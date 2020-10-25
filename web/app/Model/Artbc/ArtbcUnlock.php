<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 15 Aug 2018 14:41:22 +0800.
 */

namespace App\Model\Artbc;

use App\Model\HasMemberTrait;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ArtbcUnlock
 * 
 * @property int $id
 * @property int $member_id
 * @property float $amount
 * @property float $unlocked_amount
 * @property int $unlock_times
 * @property int $unlock_period
 * @property \Carbon\Carbon $last_unlock_day
 * @property \Carbon\Carbon $start_unlock_day
 * @property int $stat
 * @property int $creator
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Artbc
 */
class ArtbcUnlock extends Eloquent
{

	use HasMemberTrait;

	const STAT_UNLOCKING = 1;
	const STAT_DONE = 2;

	protected $casts = [
		'member_id' => 'int',
		'amount' => 'float',
		'unlocked_amount' => 'float',
		'unlock_times' => 'int',
		'unlock_period' => 'int',
		'stat' => 'int',
		'creator' => 'int'
	];

	protected $dates = [
		'last_unlock_day'
	];

	protected $fillable = [
		'member_id',
		'amount',
		'unlocked_amount',
		'unlock_times',
		'unlock_period',
		'last_unlock_day',
        'start_unlock_day',
		'stat',
		'creator'
	];

	public static function add($data) {
		return static::create($data);
	}

	public static function fetchByMemberId($member_id) {
		return static::where('member_id', $member_id)->get();
	}
}
