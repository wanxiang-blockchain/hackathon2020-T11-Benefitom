<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 11 Jan 2019 18:18:33 +0800.
 */

namespace App\Model\Btshop;

use App\Model\HasMemberTrait;
use App\Utils\DateUtil;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BlockTiqu
 * 
 * @property int $id
 * @property int $member_id
 * @property string $btaccount
 * @property int $amount
 * @property string $code
 * @property int $stat
 * @property string $auditor
 * @property string reason
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Btshop
 */
class BlockTiqu extends Eloquent
{
    use HasMemberTrait;
	protected $table = 'block_tiqu';

	const TYPE_BT = 1;
	CONST TYPE_CASH = 2;

	const STAT_INIT = 1;
	const STAT_DONE = 2;
	const STAT_REJECT = 3;

	protected $casts = [
		'member_id' => 'int',
		'amount' => 'int',
		'stat' => 'int',
        'type' => 'int',
        'price' => 'float'
	];

	protected $fillable = [
		'member_id',
		'btaccount',
		'amount',
		'code',
		'stat',
		'auditor',
        'reason',
        'type',
        'card',
        'name',
        'bank',
        'price'
	];

	public static function add($mid, $btaccount, $amount, $code, $name)
    {
        return static::create([
            'member_id' => $mid,
            'btaccount' => $btaccount,
            'amount' => $amount,
            'code' => $code,
            'name' => $name
        ]);
    }

    public static function tiCash($mid, $amount, $price, $card, $name, $bank, $code)
    {
        return static::create([
            'member_id' => $mid,
            'amount' => $amount,
            'price' => $price,
            'card' => $card,
            'name' => $name,
            'bank' => $bank,
            'code' => $code,
            'type' => self::TYPE_CASH
        ]);
    }

    public static function statLabel($stat)
    {
        switch ($stat){
            case self::STAT_INIT:
                return '提取中';
            case self::STAT_DONE:
                return '审核完成';
            case self::STAT_REJECT:
                return '驳回';
        }
    }

    public static function typeLabel($type)
    {
        switch ($type){
            case self::TYPE_BT:
                return '版通';
            case self::TYPE_CASH:
                return '现金';
        }
    }

    public static function memberTodayAmount($mid)
    {
        return static::where('member_id', $mid)
            ->where('type', self::TYPE_BT)
            ->where('created_at', '>=', DateUtil::today())
            ->sum('amount');
    }

    public static function accountTodayAmount($btaccount)
    {
        return static::where('btaccount', $btaccount)
            ->where('type', self::TYPE_BT)
            ->where('created_at', '>=', DateUtil::today())
            ->sum('amount');
    }

}
