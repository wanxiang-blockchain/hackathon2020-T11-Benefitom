<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 21 Jan 2019 14:21:35 +0800.
 */

namespace App\Model\Btshop;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Bankcard
 * 
 * @property int $id
 * @property int $member_id
 * @property string $card
 * @property string $headbank
 * @property string $bank
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Btshop
 */
class Bankcard extends Eloquent
{
	protected $casts = [
		'member_id' => 'int'
	];

	protected $fillable = [
		'member_id',
		'card',
		'headbank',
		'bank',
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

    public static function fetchWithCard($card)
    {
        return static::where('card', $card)->first();
    }
}
