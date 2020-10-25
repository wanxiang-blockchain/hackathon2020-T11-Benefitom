<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 24 Feb 2019 12:29:47 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Vip
 * 
 * @property int $id
 * @property string $phone
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Btshop
 */
class Vip extends Eloquent
{
	protected $fillable = [
		'phone'
	];

	public static function isVip($phone)
    {
        return static::where('phone', $phone)->exists();
    }

	public static function insertIfNotExists($phone)
    {
        if (static::where('phone', $phone)->exists()){
            return false;
        }
        return static::create([
            'phone' => $phone
        ]);
    }
}
