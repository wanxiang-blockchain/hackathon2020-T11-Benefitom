<?php

/**
 * Created by Reliese Model.
 * Date: Sat, 22 Dec 2018 22:49:34 +0800.
 */

namespace App\Model\Btshop;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BtshopProduct
 * 
 * @property int $id
 * @property string $name
 * @property string $img
 * @property float $price
 * @property float $rmb_price
 * @property float $bt_price
 * @property float $artbcs_price
 * @property int $paytype
 * @property float $score
 * @property int $per_limit
 * @property int $limit
 * @property int $enable
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Btshop
 */
class BtshopProduct extends Eloquent
{

    const ENABLED = 1;
    const DISABLED = 0;

    const PAYTYPE_BT = 0;
    const PAYTYPE_ARTBC = 1;
    const PAYTYPE_RMN = 2;
    const PAYTYPE_ARTBCS = 3;
    const PAYTYPE_ARTTBC_ARTBC = 4;

	protected $casts = [
		'price' => 'float',
		'score' => 'float',
        'bt_price' => 'float',
        'rmb_price' => 'float',
        'artbcs_price' => 'float',
        'paytype' => 'int',
		'per_limit' => 'int',
		'limit' => 'int',
        'enable' => 'int',
	];

	protected $fillable = [
		'name',
		'img',
		'price',
		'score',
		'per_limit',
		'limit',
        'enable',
        'bt_price',
        'rmb_price',
        'artbcs_price',
        'paytype'
	];

	public static function validePaytype($paytype)
    {
        return in_array($paytype, [
            static::PAYTYPE_BT,
            static::PAYTYPE_ARTBC,
            static::PAYTYPE_RMN,
            static::PAYTYPE_ARTBCS,
            static::PAYTYPE_ARTTBC_ARTBC
        ]);
    }

    public static function paytypes()
    {
        return [
            static::PAYTYPE_BT => 'ARTTBC',
            static::PAYTYPE_ARTBC => 'ArTBC',
            static::PAYTYPE_RMN => 'RMB',
            static::PAYTYPE_ARTBCS => 'ARTBCS + RMB',
            static::PAYTYPE_ARTTBC_ARTBC => 'ARTTBC + ArTBC'
        ];
    }

	public static function payLabel($paytype)
    {
        return static::paytypes()[$paytype];
    }

    /**
     * @param $id
     * @param array $columns
     * @return static
     */
	public static function fetchEnabelModel($id, $columns=null) {
        $query = BtshopProduct::where('id', $id)->where('enable', BtshopProduct::ENABLED);
        if ($columns){
            $query->select($columns);
        }
        return $query->first();
    }

    public function getAttributeFinalPrice()
    {
        switch ($this->paytype){
            case self::PAYTYPE_BT:
                return $this->bt_price . ' ARTTBC';
            case self::PAYTYPE_ARTBC:
                return $this->price . ' ArTBC';
            case self::PAYTYPE_RMN:
                return $this->rmb_price . ' CNY';
            case self::PAYTYPE_ARTBCS:
                return $this->artbcs_price . ' ARTBCS + ' . $this->rmb_price . ' CNY';
            case self::PAYTYPE_ARTTBC_ARTBC:
                return $this->bt_price . ' ARTTBC + ' . $this->price . ' ArTBC';
        }
    }

}
