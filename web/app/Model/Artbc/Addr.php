<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 28 Jun 2018 16:22:47 +0800.
 */

namespace App\Model\Artbc;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Addr
 * 
 * @property int $id
 * @property int $member_id
 * @property string $name
 * @property string $phone
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $addr
 * @property int $is_default
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Artbc
 */
class Addr extends Eloquent
{

	protected $casts = [
		'member_id' => 'int',
		'is_default' => 'int'
	];

	protected $fillable = [
		'member_id',
		'name',
		'phone',
		'province',
		'city',
		'area',
		'addr',
		'is_default'
	];

	/**
	 * @desc fetchByMemberId
	 * @param $member_id
	 * @return static
	 */
	public static function fetchByMemberId($member_id)
	{
		return static::where('member_id', $member_id)->orderByDesc('is_default')->first();
	}
}
