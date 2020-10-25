<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 15 Aug 2018 14:41:41 +0800.
 */

namespace App\Model\Artbc;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ArtbcUnlockLog
 * 
 * @property int $id
 * @property int $member_id
 * @property float $unlock_amount
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Artbc
 */
class ArtbcUnlockLog extends Eloquent
{
	protected $casts = [
		'member_id' => 'int',
		'unlock_amount' => 'float'
	];

	protected $fillable = [
		'member_id',
		'unlock_amount'
	];
}
