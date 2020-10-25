<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 03 Dec 2018 21:03:44 +0800.
 */

namespace App\Model\Artbc;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BtConfig
 * 
 * @property int $total_order_nums
 * @property int $per_order_nums
 * @property int $per_order_amount
 * @property int $percent
 * @property int $period
 * @property float $price
 *
 * @package App\Model\Artbc
 */
class BtConfig extends Eloquent
{
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'total_order_nums' => 'int',
		'per_order_nums' => 'int',
		'per_order_amount' => 'int',
		'percent' => 'int',
        'period' => 'int',
        'price' => 'float'
	];

	protected $fillable = [
		'total_order_nums',
		'per_order_nums',
		'per_order_amount',
		'percent',
        'period',
        'price'
	];

	public static function fetchOne()
    {
        $model = static::first();
        if (!$model) {
            $model = new static();
        }
        return $model;
    }

    public static function getPrice()
    {
        return 1;
        $model = static::fetchOne();
        return $model->price;
    }
}
