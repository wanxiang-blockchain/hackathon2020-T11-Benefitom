<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 09 May 2018 13:18:55 +0800.
 */

namespace App\Model\Artbc;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Invite
 * 
 * @property int $id
 * @property string $invite_user
 * @property string $to_user
 * @property string $invite_user_name
 * @property string $to_user_name
 * @property \Carbon\Carbon $created_at
 * @property string $msg_id
 *
 * @package App\Model\Artbc
 */
class Invite extends Eloquent
{
	protected $connection = 'artbc';
	public $timestamps = false;

	protected $fillable = [
		'invite_user',
		'to_user',
		'invite_user_name',
		'to_user_name',
		'msg_id'
	];
}
