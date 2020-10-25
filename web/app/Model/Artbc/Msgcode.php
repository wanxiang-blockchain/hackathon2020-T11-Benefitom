<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 16 Dec 2018 10:41:02 +0800.
 */

namespace App\Model\Artbc;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Msgcode
 * 
 * @property int $id
 * @property string $nationcode
 * @property string $phone
 * @property string $code
 * @property string $stat
 * @property int $expires
 * @property int $type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $ip
 *
 * @package App\Model\Artbc
 */
class Msgcode extends Eloquent
{
	protected $casts = [
		'expires' => 'int',
        'stat' => 'int'
	];

	protected $fillable = [
		'nationcode',
		'phone',
		'code',
		'expires',
		'ip',
        'stat',
        'type'
	];

	const STAT_INIT = 0;
	const STAT_USED = 1;

	public static function add($phone, $nationcode, $code, $type=0, $expires=300)
    {
        return static::create([
            'phone' => $phone,
            'nationcode' => $nationcode,
            'code' => $code,
            'expires' => time() + $expires,
            'type' => $type,
            'ip' => request()->getClientIp()
        ]);
    }

    /**
     * @param $phone
     * @param $nationcode
     * @param $code
     * @return static
     */
    public static function fetchModelWithCode($phone, $nationcode, $code, $type)
    {
        return Msgcode::where([
            'phone' => $phone,
//            'nationcode' => trim($nationcode, '+'),
            'code' => $code,
            'type' => $type,
            'stat' => self::STAT_INIT
        ])->first();
    }
}
