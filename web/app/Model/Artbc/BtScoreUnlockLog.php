<?php

/**
 * Created by Reliese Model.
 * Date: Sat, 01 Dec 2018 17:57:04 +0800.
 */

namespace App\Model\Artbc;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BtScoreUnlockLog
 * 
 * @property int $id
 * @property int $member_id
 * @property int $unlock_amount
 * @property int $bt_score_unlock_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Artbc
 */
class BtScoreUnlockLog extends Eloquent
{
	protected $casts = [
		'member_id' => 'int',
		'unlock_amount' => 'int',
        'bt_score_unlock_id' => 'int',
	];

	protected $fillable = [
		'member_id',
		'unlock_amount',
        'bt_score_unlock_id'
	];
}
