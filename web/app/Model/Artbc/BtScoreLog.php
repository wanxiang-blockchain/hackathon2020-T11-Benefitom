<?php

/**
 * Created by Reliese Model.
 * Date: Sat, 01 Dec 2018 16:16:25 +0800.
 */

namespace App\Model\Artbc;

use App\Model\HasMemberTrait;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BtScoreLog
 * 
 * @property int $id
 * @property int $member_id
 * @property int $type
 * @property float $balance
 * @property float $amount
 * @property string $stat
 * @property string $auditor
 * @property string $btaccount
 * @property string $card
 * @property string $name
 * @property string $bank
 * @property string $note
 * @property float $fee
 * @property float $shopping_score
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Artbc
 */
class BtScoreLog extends Eloquent
{
    use HasMemberTrait;

    const TYPE_UNLOCK = 1;  // 锁仓释放
    const TYPE_TIBI = 2;  // 提取
    const TYPE_TIBI_REJECT = 3;   // 提取审核驳回
    const TYPE_REC_PRIZE = 4;   // 一级推荐奖励
    const TYPE_REC_SECOND_PRIZE = 5;   // 二级推荐奖励
    const TYPE_SYSTEM_REVERT = 6;   // 系统修复


    const STAT_INIT = 0;    // 提取待审核
    const STAT_DONE = 1;  // 审核通过
    const STAT_REJECT = 2;  // 驳回

	protected $casts = [
		'member_id' => 'int',
		'type' => 'int',
		'balance' => 'float',
		'amount' => 'float',
        'fee' => 'float',
        'shopping_score' => 'float',
	];

	protected $fillable = [
		'member_id',
		'type',
		'balance',
		'amount',
		'stat',
		'auditor',
        'btaccount',
        'note',
        'fee',
        'shopping_score',
        'card',
        'name',
        'bank'
	];

	public static function add($mid, $amount, $type, $stat=1, $auditor='system', $btaccount='', $card='', $name='', $bank='')
    {
        $balance = BtScore::add($mid, $amount);
        return static::create([
            'member_id' => $mid,
            'type' => $type,
            'balance' => $balance,
            'amount' => $amount,
            'stat' => $stat,
            'auditor' => $auditor,
            'btaccount' => $btaccount,
            'card' => $card,
            'name' => $name,
            'bank' => $bank
         ]);
    }

    public static function addRecScore($mid, $amount, $type, $stat=1, $auditor='system', $btaccount='')
    {
        $balance = BtScore::addRecScore($mid, $amount);
        return static::create([
            'member_id' => $mid,
            'type' => $type,
            'balance' => $balance,
            'amount' => $amount,
            'stat' => $stat,
            'auditor' => $auditor,
            'btaccount' => $btaccount
        ]);
    }


    public static function fetchTypeLabel($type)
    {
        $map = [
            self::TYPE_UNLOCK => '锁仓释放',
            self::TYPE_TIBI => '提取',
            self::TYPE_TIBI_REJECT => '提取驳回',
            self::TYPE_REC_PRIZE => '一级推荐奖励',
            self::TYPE_REC_SECOND_PRIZE => '二级推荐奖励',
            self::TYPE_SYSTEM_REVERT => '系统修复'
        ];
        return isset($map[$type]) ? $map[$type] : '未知';
    }

    public function getTypeLabelAttribute()
    {
        return self::fetchTypeLabel($this->type);
    }


    public static function fetchStatLabel($stat)
    {
        $map = [
            self::STAT_INIT => '待审核',
            self::STAT_DONE => '审核通过',
            self::STAT_REJECT => '驳回',
        ];
        return isset($map[$stat]) ? $map[$stat] : '未知';
    }

    public function getStatLabelAttribute()
    {
        return self::fetchStatLabel($this->stat);
    }
}
