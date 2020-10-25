<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 21 Jan 2019 15:32:37 +0800.
 */

namespace App\Model\Btshop;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Aliaccount
 * 
 * @property int $id
 * @property int $member_id
 * @property string $account
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Btshop
 */
class Aliaccount extends Eloquent
{
	protected $casts = [
		'member_id' => 'int'
	];

	protected $fillable = [
		'member_id',
		'account',
		'name'
	];

    /**
     * @param $member_id
     * @return static
     */
	public static function fetchModel($member_id)
    {
        return static::where('member_id', $member_id)->first();
    }

    public static function fetchWithAccount($account)
    {
        return static::where('account', $account)->first();
    }
}
