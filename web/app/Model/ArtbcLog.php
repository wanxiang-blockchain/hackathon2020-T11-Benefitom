<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 24 Apr 2018 11:47:15 +0800.
 */

namespace App\Model;

use App\Exceptions\TradeException;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ArtbcLog
 * 
 * @property int $id
 * @property int $member_id
 * @property float $amount
 * @property float $balance
 * @property int $type
 * @property int $stat
 * @property int $auditor
 * @property string $note
 * @property string $eth_addr
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class ArtbcLog extends Eloquent
{

	use HasMemberTrait;

	const TYPE_GIFT = 1;  // 认购赠送
	const TYPE_TIBI = 2;  // 提取
	const TYPE_DIS_1 = 3;  // 一级分销奖励
	const TYPE_DIS_2 = 4;  // 二级分销奖励
	const TYPE_TIBI_REJECT = 5;   // 提取审核驳回
	const TYPE_CONSUME = 6; // 货易货
	const TYPE_UNLOCK = 7;  // 锁仓释放

	const STAT_INIT = 0;    // 提取待转账
	const STAT_DONE = 1;  // 提取转账
	const STAT_REJECT = 2;  // 驳回



	protected $casts = [
		'member_id' => 'int',
		'amount' => 'float',
		'balance' => 'float',
		'type' => 'int',
		'stat' => 'int',
		'auditor' => 'int',
	];

	protected $fillable = [
		'member_id',
		'amount',
		'balance',
		'type',
		'eth_addr',
		'stat',
		'note',
		'auditor'
	];

	public static function fetchTypeLabel($type)
	{
		$map = [
			self::TYPE_GIFT => '认购奖励',
			self::TYPE_TIBI => '提取',
			self::TYPE_DIS_1 => '一级分销奖励',
			self::TYPE_DIS_2 => '二级分销奖励',
			self::TYPE_TIBI_REJECT => '提取驳回',
			self::TYPE_CONSUME => '易货消费',
			self::TYPE_UNLOCK => '锁仓释放'
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
			self::STAT_INIT => '无',
			self::STAT_DONE => '提取完成',
			self::STAT_REJECT => '提取驳回'
		];
		return isset($map[$stat]) ? $map[$stat] : '未知';
	}

	public function getStatLabelAttribute()
	{
		return self::fetchStatLabel($this->stat);
	}

	/**
	 * @desc add
	 *       根据类型修改用户artbc存量并计算余额
	 * @param $member_id
	 * @param $amount
	 * @param $type
	 * @return bool
	 */
	public static function add($member_id, $amount, $type=self::TYPE_GIFT, $eth_addr='', $note='')
	{
		if ($type == self::TYPE_TIBI && empty($eth_addr)) {
			throw new TradeException('提取时钱包地址不可为空');
		}
		$balance = Artbc::add($member_id, $amount);
		static::create([
			'member_id' => $member_id,
			'amount' => $amount,
			'balance' => $balance,
			'stat' => 0,
			'note' => $note,
			'eth_addr' => $eth_addr,
			'type' => $type
		]);
		return true;
	}
}
