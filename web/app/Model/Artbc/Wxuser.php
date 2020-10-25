<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 06 Jul 2018 15:04:29 +0800.
 */

namespace App\Model\Artbc;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Wxuser
 * 
 * @property int $id
 * @property string $openid
 * @property string $unionid
 * @property string $appid
 * @property string $name
 * @property string $headimg
 * @property int $member_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Artbc
 */
class Wxuser extends Eloquent
{
	protected $casts = [
		'member_id' => 'int'
	];

	protected $fillable = [
		'openid',
		'unionid',
		'appid',
		'name',
		'headimg',
		'member_id'
	];

	/**
	 * @desc fetch
	 * @param $openid
	 * @return self
	 */
	public static function fetch($openid)
	{
		return static::where('openid', $openid)->first();
	}
}
