<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 22 Jan 2019 08:29:26 +0800.
 */

namespace App\Model\Btshop;

use App\Model\HasMemberTrait;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BankDraw
 * 
 * @property int $id
 * @property int $member_id
 * @property int $stat
 * @property string $order_no
 * @property float $amount
 * @property string $auditor
 * @property string $deleted_at
 * @property string $card
 * @property string $headbank
 * @property string $bank
 * @property string $name
 * @property string $note
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Btshop
 */
class BankDraw extends Eloquent
{
    use HasMemberTrait;
	use \Illuminate\Database\Eloquent\SoftDeletes;

	const STAT_INIT = 1;
	const STAT_DONE = 2;
	const STAT_REJECT = 3;

	protected $casts = [
		'member_id' => 'int',
		'stat' => 'int',
		'amount' => 'float'
	];

	protected $fillable = [
		'member_id',
		'stat',
		'order_no',
		'amount',
		'auditor',
        'card',
        'headbank',
        'bank',
        'name',
        'note'
	];

	public static function statLabel($stat)
    {
        switch($stat){
            case self::STAT_INIT:
                return '待审核';
            case self::STAT_DONE:
                return '审核通过';
            case self::STAT_REJECT:
                return '已驳回';
        }
    }

	public static function todayDrawTimes($mid)
    {
        return static::where('member_id', $mid)
            ->where('created_at', '>', date('Y-m-d'))
            ->count();
    }

    public static function todayAmount($mid)
    {
        return static::where('member_id', $mid)
            ->where('created_at', '>', date('Y-m-d'))
            ->sum('amount');
    }

    public static function todayCardAmount($card)
    {
        return static::where('card', $card)
            ->where('created_at', '>', date('Y-m-d'))
            ->sum('amount');
    }
}
