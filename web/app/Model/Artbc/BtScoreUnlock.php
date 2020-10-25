<?php

/**
 * Created by Reliese Model.
 * Date: Sat, 01 Dec 2018 17:56:57 +0800.
 */

namespace App\Model\Artbc;

use App\Exceptions\TradeException;
use App\Model\HasMemberTrait;
use App\Model\Member;
use App\Utils\DateUtil;
use App\Utils\DissysPush;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BtScoreUnlock
 * 
 * @property int $id
 * @property int $member_id
 * @property float $amount
 * @property float $unlocked_amount
 * @property int $percent
 * @property int $period
 * @property int $last_unlock_time
 * @property int $stat
 * @property int $creator
 * @property string $order_num
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Artbc
 */
class BtScoreUnlock extends Eloquent
{
    use HasMemberTrait;

    const STAT_INIT = 0;
    const STAT_UNLOCKING = 1;
    const STAT_DONE = 2;

	protected $casts = [
		'member_id' => 'int',
		'amount' => 'float',
		'unlocked_amount' => 'float',
		'percent' => 'int',
		'period' => 'int',
		'last_unlock_time' => 'int',
		'stat' => 'int',
        'creator' => 'int',
	];

	protected $fillable = [
		'member_id',
		'amount',
		'unlocked_amount',
		'percent',
		'period',
		'last_unlock_time',
		'stat',
        'creator',
        'order_code',
        'order_num'
	];

	public static function statLabel($stat)
    {
        switch ($stat){
            case self::STAT_INIT:
                return '待释放';
            case self::STAT_UNLOCKING:
                return '释放中';
            case self::STAT_DONE:
                return '释放完成';
        }
    }

	public static function orderMake()
    {
        return date('YmdH') . rand(10000000, 99999999);
    }

	public static function add($mid, $amount, $order_code, $order_num, $stat=self::STAT_DONE)
    {
        return BtScoreUnlock::create([
            'member_id' => $mid,
            'amount' => $amount,
            'percent' => 1,
            'period' => 1,
            'creator' => 'admin',
            'stat' => $stat,
            'order_code' => $order_code,
            'order_num' => $order_num
        ]);
    }

    public static function inviteAdd($mid, $amount, $order_num)
    {
        $member = Member::find($mid);
        if (!$member) {
            throw new TradeException('用户不存在');
        }
        $order_code = static::orderMake();
        if (!static::add($mid, $amount, $order_code, $order_num)) {
            throw new TradeException('数据保存失败');
        }
        // 交由直销系统计算
//        if (!DissysPush::score($order_code, $member->phone, $amount)){
//            throw new TradeException('推送积分系统失败');
//        }
    }

    public static function todayUnlockScore($member_id)
    {
        return static::where('member_id', $member_id)
            ->where('created_at', '>', DateUtil::today())
            ->sum('amount');
    }

    public static function adminUnlockAmount($member_id)
    {
        return static::where('member_id', $member_id)
            ->where('creator', '!=', '')
            ->sum('amount');
    }

}
