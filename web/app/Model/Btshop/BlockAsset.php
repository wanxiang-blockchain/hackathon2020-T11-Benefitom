<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 31 Dec 2018 10:41:02 +0800.
 */

namespace App\Model\Btshop;

use App\Exceptions\TradeException;
use App\Model\BlockTransferLog;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BlockAsset
 * 
 * @property int $id
 * @property int $member_id
 * @property string $code
 * @property float $balance
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Btshop
 */
class BlockAsset extends Eloquent
{
	protected $casts = [
		'member_id' => 'int',
		'balance' => 'float'
	];

	protected $fillable = [
		'member_id',
		'code',
		'balance'
	];

    /**
     * @param $member_id
     * @param $code
     * @return static
     */
	public static function fetchModel($member_id, $code)
    {
        return static::where('member_id', $member_id)
            ->where('code', $code)
            ->first();
    }

    /**
     * artbcs 可兑现量 = 总量 - 转入量 + 复投量 + 转出量
     * @param $member_id
     */
    public static function artbcsCanCash($member_id)
    {
        $code = BlockAssetType::CODE_ARTBCS;
        $artbcsTotal = BlockAsset::codeBalance($member_id, $code);
        $amountIn = BlockTransferLog::where('inner', $member_id)
            ->where('code', $code)
            ->where('created_at', '>', '2019-03-24')
            ->sum('amount');
        $amountPayed = abs(BlockAssetLog::where('member_id', $member_id)
            ->where('code', $code)
            ->whereIn('type', [BlockAssetLog::TYPE_CONSUME, BlockAssetLog::TYPE_TRANSFER])
            ->where('created_at', '>', '2019-03-24')
            ->sum('amount'));
        $amount = $artbcsTotal - $amountIn + $amountPayed;
        return $amount > 0 ? $amount : 0;
    }

    /**
     * @param $mid
     * @param $code
     * @return float|int
     */
    public static function codeBalance($mid, $code)
    {
        $modle = static::fetchModel($mid, $code);
        return $modle ? $modle->balance : 0;
    }

    public static function add($member_id, $amount, $code)
    {
        static::where('member_id', $member_id)
            ->where('code', $code)
            ->lockForUpdate()
            ->get();
        $model = static::fetchModel($member_id, $code);
        if (!$model) {
            $model = new static();
            $model->balance = 0;
            $model->code = $code;
            $model->member_id = $member_id;
        }
        $model->balance += $amount;
        if ($model->balance < 0){
            throw new TradeException('余额不可为负数');
        }
        if (!$model->save()){
            throw new TradeException('数据保存失败');
        }
        return $model->balance;
    }
}
