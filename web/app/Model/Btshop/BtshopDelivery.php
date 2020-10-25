<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 23 Dec 2018 12:25:04 +0800.
 */

namespace App\Model\Btshop;

use App\Model\HasMemberTrait;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BtshopDelivery
 * 
 * @property int $id
 * @property int $member_id
 * @property string $order_num
 * @property int $stat
 * @property string $receiver
 * @property string $receive_addr
 * @property string $receive_province
 * @property string $receive_city
 * @property string $receive_area
 * @property string $receive_phone
 * @property string $receive_nationcode
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $note
 * @property int $product_id
 *
 * @package App\Model\Btshop
 */
class BtshopDelivery extends Eloquent
{
    use HasMemberTrait;
	protected $table = 'btshop_delivery';

	const STAT_INIT = 1; // 待发货
    const STAT_DONE = 2; // 已发货
    const STAT_REJECT = 3; // 驳回

	protected $casts = [
		'member_id' => 'int',
		'stat' => 'int',
		'product_id' => 'int'
	];

	protected $fillable = [
		'member_id',
		'order_num',
		'stat',
		'receiver',
		'receive_addr',
		'receive_province',
		'receive_city',
		'receive_area',
		'receive_phone',
		'receive_nationcode',
		'note',
		'product_id',
        'auditor'
	];

	public static function statLabel($stat)
    {
        switch ($stat){
            case self::STAT_INIT:
                return '待发货';
            case self::STAT_DONE:
                return '已发货';
            case self::STAT_REJECT:
                return '驳回';
        }
    }

	public function product()
    {
        return $this->hasOne(BtshopProduct::class, 'id', 'product_id');
    }

    public function order()
    {
        return $this->hasOne(BtshopOrder::class, 'order_num', 'order_num');
    }

    public static function isOrderNumExist($order_num)
    {
        return static::where('order_num', $order_num)->exists();
    }
}
