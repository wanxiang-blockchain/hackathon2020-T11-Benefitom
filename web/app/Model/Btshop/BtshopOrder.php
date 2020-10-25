<?php

/**
 * Created by Reliese Model.
 * Date: Sat, 22 Dec 2018 22:49:22 +0800.
 */

namespace App\Model\Btshop;

use App\Exceptions\TradeException;
use App\Utils\DateUtil;
use App\Utils\RedisKeys;
use App\Utils\RedisUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BtshopOrder
 * 
 * @property int $id
 * @property int $member_id
 * @property string $order_num
 * @property string $tx
 * @property int $stat
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $product_id
 * @property int $price
 * @property int $amount
 * @property int $score
 * @property int $is_deliveried
 * @property int $paytype
 *
 * @package App\Model\Btshop
 */
class BtshopOrder extends Eloquent
{

    const STAT_INIT = 0;  // 初始预下单
    const STAT_TX = 1;  // 上报tx完成，加入队列
    const STAT_DONE = 2;  // 支付完成，拨分完成
    const STAT_TIMEOUTE = 3; // 支付超时

    const PAYTYPE_BT = 0;
    const PAYTYPE_ARTBC = 1;
    const PAYTYPE_RMB = 2;

    protected $casts = [
        'member_id' => 'int',
        'stat' => 'int',
        'product_id' => 'int',
        'price' => 'int',
        'amount' => 'int',
        'score' => 'int',
        'paytype' => 'int',
        'is_deliveried' => 'int'
    ];

    protected $fillable = [
        'member_id',
        'order_num',
        'tx',
        'stat',
        'product_id',
        'price',
        'amount',
        'score',
        'paytype',
        'is_deliveried'
    ];

    public static function add($orderNum, $product_id, $price, $amount, $score, $paytype, $txdata, $member_id, $stat=self::STAT_INIT)
    {
        return static::create([
            'order_num' => $orderNum,
            'product_id' => $product_id,
            'price' => $price,
            'amount' => $amount,
            'score' => $score,
            'paytype' => $paytype,
            'txdata' => $txdata,
            'member_id' => $member_id,
            'stat' => $stat
        ]);
    }

    public static function todayBoughtCount($member_id, $product_id)
    {
        return static::where('member_id', $member_id)->where('product_id', $product_id)
            ->whereIn('stat', [BtshopOrder::STAT_DONE, BtshopOrder::STAT_TX])
            ->where('created_at', '>', DateUtil::today())
            ->sum('amount');
    }

    /**
     * @param $orderNum
     * @return static
     */
    public static function fetchModelByOrderNum($orderNum)
    {
        return static::where('order_num', $orderNum)->first();
    }

    /**
     * @param $tx
     * @return bool
     */
    public static function isTxExist($tx)
    {
        return static::where('tx', $tx)->exists();
    }

    public function product()
    {
        return $this->hasOne(BtshopProduct::class, 'id', 'product_id');
    }

    public function orderDelivery()
    {
        return $this->hasOne(BtshopDelivery::class, 'order_num', 'order_num');
    }

    public static function buySumPay($paytype)
    {
        $today = DateUtil::todayDate();

        switch ($paytype){
            case BtshopProduct::PAYTYPE_BT:
                $key = RedisKeys::BTSHOP_ORDER_BT_SUM . $today;
                break;
            case BtshopProduct::PAYTYPE_ARTBC:
                $key = RedisKeys::BTSHOP_ORDER_ARTBC_SUM . $today;
                break;
            case BtshopProduct::PAYTYPE_RMN:
                $key = RedisKeys::BTSHOP_ORDER_RMN_SUM . $today;
                break;
            case BtshopProduct::PAYTYPE_ARTBCS:
                $key = RedisKeys::BTSHOP_ORDER_ARTBCS_SUM . $today;
                break;
            default:
                throw new TradeException('error paytype of buySumPay');
        }
        $amount = RedisUtil::get($key);
        Log::info('buySumBtPay today amount:' . $amount);
        if (empty($amount)){
            $amount = floatval(BtshopOrder::where('created_at', '<', DateUtil::today())
                ->where('stat', BtshopOrder::STAT_DONE)
                ->where('paytype', $paytype)
                ->value(DB::raw('sum(amount * score)')));
            RedisUtil::set($key, $amount, 86400);
        }
        return $amount;
    }

    public static function todayPayAmount($paytype)
    {
        $today = DateUtil::today();
        $amount = floatval(BtshopOrder::where('created_at', '>=', $today)
            ->where('stat', BtshopOrder::STAT_DONE)
            ->where('paytype', $paytype)
            ->value(DB::raw('sum(amount * score)')));
        return $amount;
    }


    /**
     * 是否有过 paytype = 1 购买下单
     */
    public static function paytypeAmount($mid, $paytype = self::PAYTYPE_ARTBC)
    {
        return static::where('member_id', $mid)
            ->where('paytype', $paytype)
            ->where('stat', self::STAT_DONE)
            ->sum(DB::raw('amount * score'));
    }

}
