<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 07 Jan 2019 14:28:47 +0800.
 */

namespace App\Model\Btshop;

use App\Model\HasMemberTrait;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BlockAssetExchangeLog
 * 
 * @property int $id
 * @property int $member_id
 * @property string $code
 * @property int $stat
 * @property float $amount
 * @property float $price
 * @property string $auditor
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property string $order_code
 *
 * @package App\Model\Btshop
 */
class BlockAssetExchangeLog extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	use HasMemberTrait;

	const STAT_INIT = 1; // 兑换待审核
    const STAT_DONE = 2;  // 兑换完成
    const STAT_REJECT = 3; // 兑换拒绝

	protected $casts = [
		'member_id' => 'int',
		'stat' => 'int',
		'amount' => 'float',
        'price' => 'float'
	];

	protected $fillable = [
		'member_id',
		'code',
		'stat',
		'amount',
		'auditor',
        'order_code',
        'price'
	];

    /**
     * @param $order_code
     * @return static
     */
	public static function fetchByOrderCode($order_code)
    {
        return static::where('order_code', $order_code)->first();
    }

    public static function orderExist($order_code)
    {
        return static::where('order_code', $order_code)->exists();
    }

	public static function add($mid, $code, $amount, $order_code, $price, $stat = self::STAT_DONE )
    {
        return static::create([
            'member_id' => $mid,
            'code' => $code,
            'amount' => $amount,
            'stat' => $stat,
            'order_code' => $order_code,
            'price' => $price
        ]);
    }
}
