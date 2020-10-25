<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 27 Jan 2019 22:04:05 +0800.
 */

namespace App\Model\Btshop;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class SuperMember
 * 
 * @property int $id
 * @property string $phone
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Btshop
 */
class SuperMember extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;

	protected $fillable = [
		'phone'
	];

	public static function isSuper($phone)
    {
        return static::where('phone', $phone)->exists();
    }
}
