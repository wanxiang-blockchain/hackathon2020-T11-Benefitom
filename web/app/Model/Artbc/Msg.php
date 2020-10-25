<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 09 May 2018 13:33:23 +0800.
 */

namespace App\Model\Artbc;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Msg
 * 
 * @property int $id
 * @property string $msg
 * @property string $from_user
 * @property string $to_user
 * @property string $con
 * @property string $from_user_name
 * @property string $to_user_name
 * @property string $msg_id
 * @property \Carbon\Carbon $created_at
 *
 * @package App\Model\Artbc
 */
class Msg extends Eloquent
{
	protected $connection = 'artbc';
	public $timestamps = false;

	protected $fillable = [
		'msg',
		'from_user',
		'to_user',
		'con',
		'from_user_name',
		'to_user_name',
		'msg_id'
	];
}
