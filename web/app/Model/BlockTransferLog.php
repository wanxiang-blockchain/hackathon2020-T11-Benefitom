<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 22 Feb 2019 21:19:07 +0800.
 */

namespace App\Model;

use App\Model\Btshop\BlockAsset;
use App\Model\Btshop\BlockAssetLog;
use App\Model\Btshop\BlockAssetType;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BlockTransferLog
 * 
 * @property int $id
 * @property int $outer
 * @property int $inner
 * @property int $amount
 * @property string $code
 * @property string $order_no
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @package App\Model\Btshop
 */
class BlockTransferLog extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;

	protected $casts = [
		'outer' => 'int',
		'inner' => 'int',
		'amount' => 'int'
	];

	protected $fillable = [
		'outer',
		'inner',
		'amount',
		'code',
		'order_no'
	];

    /**
     * 转入减掉复投
     * @param $member_id
     * @param string $code
     * @return mixed
     */
	public static function amountIn($member_id, $code='300002') {
	    $transferIn =  static::where('inner', $member_id)
            ->where('code', $code)
            ->sum('amount');
	    $arttbcPaySmount = abs(BlockAssetLog::where('member_id', $member_id)
            ->where('code', $code)
            ->whereIn('type', [BlockAssetLog::TYPE_CONSUME, BlockAssetLog::TYPE_TI_BT])
            ->where('created_at', '>', '2019-03-11')
            ->sum('amount'));

	    return $transferIn > $arttbcPaySmount ? $transferIn - $arttbcPaySmount : 0;
    }
}
