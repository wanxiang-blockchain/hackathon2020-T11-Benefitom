<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 31 Dec 2018 11:16:23 +0800.
 */

namespace App\Model\Btshop;

use App\Exceptions\TradeException;
use App\Model\HasMemberTrait;
use mysql_xdevapi\Exception;
use Reliese\Database\Eloquent\Model as Eloquent;
use spec\Prophecy\Exception\Prophecy\MethodProphecyExceptionSpec;

/**
 * Class BlockRechargeLog
 * 
 * @property int $id
 * @property int $member_id
 * @property string $code
 * @property int $stat
 * @property string $account
 * @property string $tx
 * @property float $amount
 * @property string $order_num
 * @property string $txdata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Btshop
 */
class BlockRechargeLog extends Eloquent
{
    use HasMemberTrait;

    const STAT_INIT = 0;   // 初始化
    const STAT_ING = 1;   // 支付中
    const STAT_DONE = 2;  // 支付完成
    const STAT_TIMEOUT = 3;   // 支付超时
    const STAT_UNEXCEPT_PAY = 4;   // 异常支付
    const STAT_FAILED = 5;   // 异常失败

	protected $casts = [
		'member_id' => 'int',
		'stat' => 'int',
		'amount' => 'float'
	];

	protected $fillable = [
		'member_id',
		'code',
		'stat',
		'account',
		'tx',
		'amount',
		'order_num',
        'txdata'
	];

	public static function statLabel($stat)
    {
        switch ($stat){
            case self::STAT_INIT:
                return '待支付';
            case self::STAT_ING:
                return '支付中';
            case self::STAT_DONE:
                return '完成';
            case self::STAT_TIMEOUT:
                return '支付超时';
            case self::STAT_UNEXCEPT_PAY:
                return '异常支付';
            case self::STAT_FAILED:
                return '支付失败';
            default:
                return '未知';
        }
    }

	public static function add($member_id, $code, $account, $amount, $order_num, $txdata='')
    {
        return static::create([
            'member_id' => $member_id,
            'code' => $code,
            'account' => $account,
            'amount' => $amount,
            'order_num' => $order_num,
            'txdata' => $txdata
        ]);
    }

    /**
     * @param $order_num
     * @return static
     */
    public static function fetchByOrderNum($order_num)
    {
        return static::where('order_num', $order_num)->first();
    }

    /**
     * @param $tx
     * @param $code
     * @return static
     */
    public static function fetchByTxCode($tx, $code)
    {
        if (empty($tx)){
            return null;
        }
        return static::where('tx', $tx)->where('code', $code)->first();
    }

    public static function txExists($tx)
    {
        if (empty($tx)){
            throw new TradeException('empty tx of in method txExists');
        }
        return static::where('tx', $tx)->exists();
    }
}
