<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 24 Apr 2019 22:17:56 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BlockTibi
 * 
 * @property int $id
 * @property int $member_id
 * @property string $order_no
 * @property int $amount
 * @property string $addr
 * @property int $stat
 * @property string $auditor
 * @property string $note
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model
 */
class BlockTibi extends Eloquent
{
    use HasMemberTrait;

    const STAT_INIT = 0;
    const STAT_DONE = 1;
    const STAT_REJECT = 2;

	protected $casts = [
		'member_id' => 'int',
		'amount' => 'int',
		'stat' => 'int'
	];

	protected $fillable = [
		'member_id',
		'order_no',
		'amount',
		'addr',
		'stat',
		'auditor',
        'note'
	];

	public static function statLabel($stat)
    {
        $map = [
            self::STAT_INIT => '待转账',
            self::STAT_DONE => '已转',
            self::STAT_REJECT => '已驳回'
        ];
        return isset($map[$stat]) ? $map[$stat] : '未知';
    }

	public static function add($member_id, $order_no, $amount, $addr)
    {
        return static::create([
            'member_id' => $member_id,
            'order_no' => $order_no,
            'amount' => $amount,
            'addr' => $addr
        ]);
    }
}
