<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 09 May 2018 13:17:15 +0800.
 */

namespace App\Model\Artbc;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Eth
 * 
 * @property int $id
 * @property string $addr
 * @property string $user
 * @property string $nickname
 * @property string $msg_id
 * @property \Carbon\Carbon $created_at
 *
 * @package App\Model\Artbc
 */
class Eth extends Eloquent
{
	protected $connection = 'artbc';
	protected $table = 'eth';
	public $timestamps = false;

	protected $fillable = [
		'addr',
		'user',
		'nickname',
		'msg_id'
	];
}
