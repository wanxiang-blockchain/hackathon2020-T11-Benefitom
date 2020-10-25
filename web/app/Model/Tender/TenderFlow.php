<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/16
 * Time: 上午11:35
 */

namespace App\Model\Tender;


use App\Model\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TenderFlow extends Model
{

	protected $table = 'tender_flow';
	protected $fillable = [
		'member_id', 'amount', 'after_amount', 'type', 'content'
	];

	const TYPE_RECHARGE = 1;  // 充值
	const TYPE_WITHDRAW = 2;  // 提现
	const TYPE_GUESS    = 3;  // 估价
	const TYPE_MARGIN_PAY = 4;   // 缴纳保证金
	const TYPE_MARGIN_BACK = 6; // 退还保证金
	const TYPE_GUESS_PRIZE_WINNER = 7;  // 估价中奖火眼金睛奖
	const TYPE_GUESS_PRIZE_FIRST = 8;  // 估价中奖金睛奖
	const TYPE_ADMIN_RECHARGE = 9;   // 管理员充值
	const TYPE_WITHDRAW_REJECT = 10;   // 提现驳回
	const TYPE_SIGNUP_GIFT  = 11;   // 连续签到3到以上赠送
	const TYPE_ADMIN_REG_GIFT = 12;   // 注册赠送-管理员后台操作
	const TYPE_GONGPAN_PRIZE = 13;   // 绍德堂认购藏品赠送

	public static function types()
	{
		return [
			self::TYPE_RECHARGE => '充值',
			self::TYPE_WITHDRAW => '提现',
			self::TYPE_GUESS =>  '估价',
			self::TYPE_MARGIN_PAY => '缴纳保证金',
			self::TYPE_MARGIN_BACK => '退还保证金',
			self::TYPE_GUESS_PRIZE_WINNER => '估价中奖火眼金睛奖红包',
			self::TYPE_GUESS_PRIZE_FIRST => '估价中奖金睛奖红包',
			self::TYPE_ADMIN_RECHARGE => '管理员充值',
			self::TYPE_WITHDRAW_REJECT => '提现驳回',
			self::TYPE_SIGNUP_GIFT => '签到赠送小红花',
			self::TYPE_ADMIN_REG_GIFT => '注册赠送',
			self::TYPE_GONGPAN_PRIZE => '绍德堂认购藏品赠送'
		];
	}

	public function defCon()
	{
		$map = static::types();
		return isset($map[$this->type]) ? $map[$this->type] : '其他';
	}

	/**
	 * 返回用户所赠送小红花
	 * @desc giftCount
	 * @param $member_id
	 * @return int
	 */
	public static function giftCount($member_id)
	{
		$row = DB::select("select sum(amount) as amount from tender_flow where member_id = ? and  type in (11, 12, 13) ;", [$member_id]);
		return intval($row[0]->amount);
	}

	public function member()
	{
		return $this->hasOne(Member::class, 'id', 'member_id');
	}
}