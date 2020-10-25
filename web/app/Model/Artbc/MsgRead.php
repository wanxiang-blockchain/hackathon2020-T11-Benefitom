<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 05 Dec 2018 14:27:24 +0800.
 */

namespace App\Model\Artbc;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class MsgRead
 * 
 * @property int $id
 * @property int $member_id
 * @property int $push_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Artbc
 */
class MsgRead extends Eloquent
{
    protected $connection = 'cms';
	protected $casts = [
		'member_id' => 'int',
		'push_id' => 'int'
	];

	protected $fillable = [
		'member_id',
		'push_id'
	];

	public static function readed($mid, $push_id) {
	    return static::where('member_id', $mid)
            ->where('push_id', $push_id)
            ->exists();
    }
}
