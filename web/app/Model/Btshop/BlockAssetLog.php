<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 31 Dec 2018 10:41:09 +0800.
 */

namespace App\Model\Btshop;

use App\Model\HasMemberTrait;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BlockAssetLog
 * 
 * @property int $id
 * @property int $member_id
 * @property string $code
 * @property float $balance
 * @property float $amount
 * @property int $type
 * @property string $desc
 * @property string $order_no
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Btshop
 */
class BlockAssetLog extends Eloquent
{

    use HasMemberTrait;

    const TYPE_RECHARGE = 1; // 充值
    const TYPE_CONSUME  = 2; // 消费
    const TYPE_EXCHANGE = 3;  // 兑换，来自直销系统
    const TYPE_RECHARGE_REVISE = 4; // 充值未到修正
    const TYPE_RECHARGE_DEL = 5; // 异常充值扣除
    const TYPE_TI_BT = 6;  // 提取为版通
    const TYPE_TI_BT_REJECT = 7;  // 提取为版通驳回
    const TYPE_TI_CASH = 8;  // 提取为现金
    const TYPE_TI_CASH_REJECT = 9;  // 提取为现金驳回
    const TYPE_TO_RMB  = 10;  //  提为现金余额
    const TYPE_TRANSFER  = 11;  //  转账给用户
    const TYPE_TRANSFER_IN  = 12;  //  由用户转入

    const TYPE_TO_ARTTBC = 20;  // 兑换为ARTTBC
    const TYPE_TO_RMB_ARTBCS = 21;  // 兑换为现金+ARTBCS
    const TYPE_TO_RMB_ARTTBC = 22;  // 兑换为现金+ARTTBC

    const TYPE_FROM_ARTTBC = 23;  // 来自 ARTTBC兑换
    const TYPE_FROM_ARTBCS = 24;  // 来自 ARTBCS兑换
    const TYPE_FROM_CASH = 26;  // 来自现金余额兑换

    const TYPE_TO_ARTBC = 25;  // 兑换为ARTBC

    const ALIPAY_BUY_ARTTBC = 30;  // 支付宝购买
    const SALE_ARTTBC = 31;  // 售出

    const TYPE_TI_BI = 32;  // ArTBC提币
    const TYPE_TI_BI_REJECT = 33;  // ArTBC提币驳回

    const TYPE_P_PRIZE = 34;  // 一级奖励
    const TYPE_PP_PRIZE = 35; // 二级奖励

	protected $casts = [
		'member_id' => 'int',
		'balance' => 'float',
		'amount' => 'float',
		'type' => 'int'
	];

	protected $fillable = [
		'member_id',
		'code',
		'balance',
		'amount',
		'type',
		'desc'
	];

	public static function codeToName($code)
    {
        return BlockAssetType::codeToName($code);
    }

    public static function typeMaps()
    {
        return [
            self::TYPE_RECHARGE => '充值',
            self::TYPE_CONSUME => '消费',
            self::TYPE_EXCHANGE => '积分兑换',
            self::TYPE_RECHARGE_REVISE => '充值未到修正',
            self::TYPE_RECHARGE_DEL => '异常充值修正',
            self::TYPE_TI_BT => '提取为版通',
            self::TYPE_TI_BT_REJECT => '提取版通驳回',
            self::TYPE_TO_RMB => '提为现金余额',
            self::TYPE_TRANSFER => '转出到用户',
            self::TYPE_TRANSFER_IN => '用户转入',
            self::TYPE_TO_ARTTBC => '兑换为ARTTBC',
            self::TYPE_TO_RMB_ARTBCS => '兑换为现金+ARTBCS',
            self::TYPE_TO_RMB_ARTTBC => '兑换为现金+ARTTBC',
            self::TYPE_FROM_ARTTBC => '兑换自ARTTBC',
            self::TYPE_FROM_ARTBCS => '兑换自ARTBCS',
            self::TYPE_TO_ARTBC => '兑换为ArTBC',
            self::TYPE_FROM_CASH => '兑换自现金余额',
            self::ALIPAY_BUY_ARTTBC => '支付宝购买',
            self::SALE_ARTTBC => '售出',
            self::TYPE_TI_BI => '提币',
            self::TYPE_TI_BI_REJECT => '提币驳回',
            self::TYPE_P_PRIZE => '一级奖励',
            self::TYPE_PP_PRIZE => '二级奖励'
        ];
    }

    public static function fetchTypeLable($type)
    {
        $map = static::typeMaps();
        return isset($map[$type]) ? $map[$type] : '未知';
    }

	public static function record($member_id, $code, $amount, $type, $desc='', $order_no='')
    {
        return static::create([
            'member_id' => $member_id,
            'code' => $code,
            'amount' => $amount,
            'type' => $type,
            'desc' => $desc,
            'balance' => BlockAsset::add($member_id, $amount, $code),
            'order_no' => $order_no
        ]);
    }
}
