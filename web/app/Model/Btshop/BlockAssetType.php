<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 31 Dec 2018 10:41:05 +0800.
 */

namespace App\Model\Btshop;

use App\Exceptions\TradeException;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BlockAssetType
 * 
 * @property int $id
 * @property string $code
 * @property string $name
 * @property float $market_value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Btshop
 */
class BlockAssetType extends Eloquent
{

    const CODE_ARTBC = '300001';
    const CODE_ARTTBC = '300002';
    const CODE_ARTBCS = '300003';

	protected $casts = [
		'market_value' => 'float'
	];

	protected $fillable = [
		'code',
		'name',
		'market_value'
	];

	public static function codes()
    {
	    return [
	        self::CODE_ARTBC => 'ArTBC',
            self::CODE_ARTTBC => 'ARTTBC',
            self::CODE_ARTBCS => 'ARTBCS'
        ];
    }

    public static function codeToName($code)
    {
        $map = static::codes();
        if (empty($map[$code])){
            throw new TradeException('Unexcepted asset code');
        }
        return $map[$code];
    }

	public static function valideCode($code)
    {
        if (empty($code)) {
            return false;
        }
        return isset(static::codes()[$code]);
    }
}
