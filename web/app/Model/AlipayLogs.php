<?php

namespace App\Model;

use App\Utils\DateUtil;
use App\Utils\RedisKeys;
use App\Utils\RedisUtil;
use Illuminate\Database\Eloquent\Model;

class AlipayLogs extends Model
{

	const TYPE_ALI = 1;
	const TYPE_WX = 2;

    //
    public $table = 'alipay_logs';
    protected $fillable = [
        'type','order_id', 'member_id','status', 'content', 'money', 'poundage', 'real_money'
    ];


    /**
     * 获取累计提款总量
     * @return int
     */
    public static function totalSum()
    {
        $today = DateUtil::todayDate();
        $key = RedisKeys::ALIPAY_RECHARGE_SUM . $today;

        $amount = RedisUtil::get($key);
        if (empty($amount)) {
            $amount = static::where('created_at', '<', DateUtil::today())
                ->where('status', 1)
                ->sum('money');
            RedisUtil::set($key, $amount, 86400);
        }
        return $amount;
    }

    public static function todaySum()
    {
        $today = DateUtil::today();
        $amount = static::where('created_at', '>=', $today)
            ->where('status', 1)
            ->sum('money');
        return $amount;
    }
}
