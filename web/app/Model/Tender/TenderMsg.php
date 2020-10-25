<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/11/1
 * Time: 上午10:26
 */

namespace App\Model\Tender;


use Illuminate\Database\Eloquent\Model;

class TenderMsg extends Model
{
//	protected $table = '';
	protected $fillable = ['con', 'member_id', 'has_read', 'title', 'type', 'ext', 'temp_id'];

	const TITLE_FLOW = '小红花变动';
	const TITLE_DEAL = '竞拍成功';

	const TYPE_FLOW = 1;  // 小红花流水
	const TYPE_DEAL = 2;  // 拍卖成交

	const TEMP_AWARD = 1;   // 获奖消息模板
	const TEMP_TENDER = 2;  // 拍中消息模板
	const TEMP_INVITE_REG = 3;   // 受邀请注册
	const TEMP_FIRST_SIGN  = 4;  // 首次签到，赠送小红花
	const TEMP_SIGNUP  = 5;   // 连续签到，赠送小红花
	const TEMP_WITHDARW_REJECT  = 6;   // 提现驳回

	public static function add($member_id, $temp_id, $ext = '')
	{
		$type = in_array($temp_id, [self::TEMP_TENDER]) ? self::TYPE_DEAL : self::TYPE_FLOW;
		static::create([
			'member_id' => $member_id,
			'has_read' => 0,
			'title' => static::fetchTitleWithType($type),
			'type' => $type,
			'ext' => $ext,
			'temp_id' => $temp_id,
		]);
	}

	public static function fetchTitleWithType($type)
	{
		switch ($type){
			case static::TYPE_FLOW:
				return static::TITLE_FLOW;
			case static::TYPE_DEAL:
				return static::TITLE_DEAL;
			default:
				return static::TITLE_FLOW;
		}
	}

	public function read()
	{
		$this->has_read = 1;
		return $this->save();
	}

	public static function unReadCount($member_id)
	{
		return static::where('member_id', $member_id)
			->where('has_read', 0)
			->count();
	}

	public function temp()
	{
		$tempMap = [
			self::TEMP_AWARD => 'tempAward',
			self::TEMP_TENDER => 'tempTender',
			self::TEMP_INVITE_REG => 'tempInviteReg',
			self::TEMP_FIRST_SIGN  => 'tempFirstSign',
			self::TEMP_SIGNUP  => 'tempSignup',
			self::TEMP_WITHDARW_REJECT => 'tempWithdrawReject'
		];
		$ext = json_decode($this->ext, true);
		$func = $tempMap[$this->temp_id];

		return $this->$func($ext);
	}

	public function tempAward($ext)
	{
		$price =  $ext['winner_type'] == 1 ? '火眼金晴奖' : '金晴奖';
		return '恭喜您！在艺术品' . $ext['name'] . '的鉴赏考试中荣获“' . $price . '”，获得' . $ext['award'] . '朵小红花奖励，可前往小红花记录查看。';
	}

	public static function setTempAward($member_id, $name, $winner_type, $award)
	{
		$ext = get_defined_vars();
		unset($ext['member_id']);
		return static::add($member_id, self::TEMP_AWARD, json_encode($ext));
	}

	public function tempTender($ext)
	{
		return '恭喜您！在艺术品”' . $ext['name'] . '”的拍卖中以' . $ext['price'] . '元竞拍成功，稍后工作人员会与您联系，请保持注册手机号畅通。';
	}

	public static function setTempTender($member_id, $name, $price, $tender_log_id)
	{
		$ext = get_defined_vars();
		unset($ext['member_id']);
		return static::add($member_id, self::TEMP_TENDER, json_encode($ext));
	}

	public function tempInviteReg($ext)
	{
		return '恭喜您！受邀请注册艺奖堂，赠送中' . $ext['amount'] . '朵小红花，可前往小红花记录查看。';
	}

	public static function setTempInviteReg($member_id, $amount)
	{
		$ext = get_defined_vars();
		unset($ext['member_id']);
		return static::add($member_id, self::TEMP_INVITE_REG, json_encode($ext));
	}

	public function tempFirstSign($ext)
	{
		return '恭喜您，首次签到奖励小红花' . $ext['amount'] . '朵已存入您的书包中，可前往小红花记录查看。';
	}

	public static function setTempFirstSign($member_id, $amount)
	{
		$ext = get_defined_vars();
		unset($ext['member_id']);
		return static::add($member_id, self::TEMP_FIRST_SIGN, json_encode($ext));
	}

	public function tempSignup($ext)
	{
		return '恭喜您！连续签到' . $ext['addup'] . '天，奖励' . $ext['amount'] . '朵小红花已存入您的书包中，可前往小红花记录查看。';
	}

	public static function setTempSignup($member_id, $addup, $amount)
	{
		$ext = get_defined_vars();
		unset($ext['member_id']);
		return static::add($member_id, self::TEMP_SIGNUP, json_encode($ext));
	}

	public function tempWithdrawReject($ext)
	{
		return '尊敬的用户，你在' . $ext['created_at'] . '进行的' . $ext['amount'] . '元提现申请被管理员驳回，驳回原因：' . $ext['note'] . '。如有疑问，请联系客服。';
	}

	public static function setTempWithdarwReject($member_id, $created_at, $amount, $note)
	{
		$ext = get_defined_vars();
		unset($ext['member_id']);
		return static::add($member_id, self::TEMP_WITHDARW_REJECT, json_encode($ext));
	}

}