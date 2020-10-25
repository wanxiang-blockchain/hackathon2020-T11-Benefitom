<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 21 Jan 2019 19:31:07 +0800.
 */

namespace App\Model\Btshop;

use App\Model\HasMemberTrait;
use App\Utils\DateUtil;
use App\Utils\RedisKeys;
use App\Utils\RedisUtil;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AlipayDraw
 * 
 * @property int $id
 * @property int $member_id
 * @property int $stat
 * @property string $order_no
 * @property float $amount
 * @property string $exdata
 * @property string $account
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Btshop
 */
class AlipayDraw extends Eloquent
{

    const STAT_DONE = 1; // 成功
    const STAT_FAIL = 2;  // 失败
    const STAT_DOING = 3; // 待确定状态

    use HasMemberTrait;
    use \Illuminate\Database\Eloquent\SoftDeletes;

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
        'exdata',
        'account'
    ];

    public static function statLabel($stat)
    {
        switch ($stat) {
            case self::STAT_FAIL:
                return '失败';
            case self::STAT_DONE:
                return '完成';
            case self::STAT_DOING:
                return '提款中';
        }
    }

    /**
     * 获取累计提款总量
     * @return int
     */
    public static function totalSum()
    {
        $today = DateUtil::todayDate();
        $key = RedisKeys::ALIPAY_DRAW_SUM . $today;

        $amount = RedisUtil::get($key);
        if (empty($amount)) {
            $amount = static::where('created_at', '<', DateUtil::today())
                ->sum('amount');
            RedisUtil::set($key, $amount, 86400);
        }
        return $amount;
    }

    public static function todaySum()
    {
        $today = DateUtil::today();
        $amount = static::where('created_at', '>=', $today)
            ->sum('amount');
        return $amount;
    }

    /**
     * @param $member_id
     * @return mixed
     */
    public static function todayAmount($member_id)
    {
        return static::where('member_id', $member_id)
            ->where('created_at', '>=', date('Y-m-d'))
//            ->where('stat', self::STAT_DONE)
            ->sum('amount');
    }

    /**
     * @param $member_id
     * @return mixed
     */
    public static function accountTodayAount($account)
    {
        return static::where('account', $account)
            ->where('created_at', '>', date('Y-m-d'))
            ->sum('amount');
    }

    /**
     * 取今天单 member_id 提现数量
     * @param $member_id
     * @return int
     */
    public static function todayMemberAmount($member_id)
    {
        $key = RedisKeys::ALIPAY_DRAW_AMOUNT_LIMIT_PRE . 'member:' . $member_id . ':' . DateUtil::todayDate();
        return intval(RedisUtil::get($key));
    }

    /**
     * @param $aliaccount
     * @return int
     */
    public static function todayAliaccountAmount($aliaccount)
    {
        $key = RedisKeys::ALIPAY_DRAW_AMOUNT_LIMIT_PRE . 'aliaccount:' . $aliaccount . ':' . DateUtil::todayDate();
        return intval(RedisUtil::get($key));
    }

    /**
     * @param $member_id
     * @param $amount
     */
    public static function todayMemberIncrby($member_id, $amount)
    {
        $amount = intval($amount);
        $key = RedisKeys::ALIPAY_DRAW_AMOUNT_LIMIT_PRE . 'member:' . $member_id . ':' . DateUtil::todayDate();
        RedisUtil::incrby($key, $amount, 86400);
    }

    /**
     * @param $member_id
     * @param $amount
     */
    public static function todayIdnoIncrby($idno, $amount)
    {
        if (empty($idno) || empty($amount)) {
            return 0;
        }
        $amount = intval($amount);
        $key = RedisKeys::ALIPAY_DRAW_AMOUNT_LIMIT_PRE . 'idno:' . $idno . ':' . DateUtil::todayDate();
        RedisUtil::incrby($key, $amount, 86400);
    }

    /**
     * 取今天单 member_id 提现数量
     * @param $member_id
     * @return int
     */
    public static function todayIdnoAmount($idno)
    {
        $key = RedisKeys::ALIPAY_DRAW_AMOUNT_LIMIT_PRE . 'idno:' . $idno . ':' . DateUtil::todayDate();
        return intval(RedisUtil::get($key));
    }

    /**
     * @param $aliaccount
     * @param $amount
     */
    public static function todayAliaccountAIncrby($aliaccount, $amount)
    {
        $amount = intval($amount);
        $key = RedisKeys::ALIPAY_DRAW_AMOUNT_LIMIT_PRE . 'aliaccount:' . $aliaccount . ':' . DateUtil::todayDate();
        RedisUtil::incrby($key, $amount, 86400);
    }
}


