<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 18 Apr 2019 20:19:15 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BlockSale
 * 
 * @property int $id
 * @property int $member_id
 * @property string $name
 * @property string $card
 * @property string $bank
 * @property int $amount
 * @property int $stat
 * @property string $admin
 * @property string $order_no
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model
 */
class BlockSale extends Eloquent
{
    use HasMemberTrait;

    const STAT_INIT = 0;
    const STAT_DONE = 1;

	protected $casts = [
		'member_id' => 'int',
		'amount' => 'int',
		'stat' => 'int'
	];

	protected $fillable = [
		'member_id',
		'name',
		'card',
		'bank',
		'amount',
		'stat',
		'admin',
        'order_no'
	];

    /**
     * @param $member_id
     * @return static
     */
	public static function fetchByMid($member_id)
    {
        return static::where('member_id', $member_id)->first();
    }

    public static function add($mid, $amount, $name, $card, $bank, $order_no)
    {
        static::create([
           'member_id' => $mid,
           'amount' => $amount,
           'name' => $name,
           'card' => $card,
           'bank' => $bank,
           'order_no' => $order_no,
           'stat' => 0
        ]);
    }

    public static function statLabel($stat)
    {
        $map = [
            self::STAT_INIT => '待打款',
            self::STAT_DONE => '已打款'
        ];

        return isset($map[$stat]) ? $map[$stat] : '未知';
    }
}
