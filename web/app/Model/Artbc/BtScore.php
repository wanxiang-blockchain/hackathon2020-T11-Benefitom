<?php

/**
 * Created by Reliese Model.
 * Date: Sat, 01 Dec 2018 16:10:56 +0800.
 */

namespace App\Model\Artbc;

use App\Model\HasMemberTrait;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BtScore
 * 
 * @property int $id
 * @property int $member_id
 * @property float $score
 * @property float $fee
 * @property float $shopping_score
 * @property float $rec_score
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Artbc
 */
class BtScore extends Eloquent
{
    use HasMemberTrait;

	protected $casts = [
		'member_id' => 'int',
		'score' => 'float',
		'fee' => 'float',
		'shopping_score' => 'float',
        'rec_score' => 'float',
	];

	protected $fillable = [
		'member_id',
		'score',
        'fee',
        'shopping_score',
        'rec_score'
	];

    public static function add($member_id, $amount)
    {
        $model = static::where('member_id', $member_id)->first();
        if (!$model) {
            $model = new static();
            $model->member_id = $member_id;
        }
        $model->score += $amount;
        if (!$model->save()) {
            throw new TradeException('释放版通失败');
        }
        return $model->score;
    }

    /**
     * @param $id
     * @return static
     */
    public static function fetchByMemberId($member_id)
    {
        return static::where('member_id', $member_id)->first();
    }

    public static function addRecScore($member_id, $score)
    {
        $model = static::fetchByMemberId($member_id);
        if (!$model){
            $model = new static();
            $model->member_id = $member_id;
            $model->rec_score = 0;
        }
        $model->rec_score += $score;
        $model->save();
        return $model->rec_score;
    }

}
