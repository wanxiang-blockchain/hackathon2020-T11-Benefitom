<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = [
        'member_id', 'type', 'asset_type', 'content', 'balance', 'amount', 'after_amount', 'order_no'
    ];

	/**
	 * code
	 */
    const RE_CHANGE = 2; //  充值
    const WITHDRAW = 4;  // 提现
	const SCORE     = 7;  // 交易利润兑换积分
	const RONG      = 8; // 艺融宝消费
	const RONG_RETURN = 9; // 艺融宝反利
	const ADMIN_WITH_DRAW = 20; // 管理员提现
	const ADMIN_WITH_DRAW_REJECT = 21; // 管理员提现驳回
    const WALLET_SALE_COST = 22; // 艺行派促销中心消费
    const WALLET_ALIPAY_DRAW = 23; // 艺行派提现到支付宝
    const WALLET_ARTTBC_EXCHANGE = 24;  // 艺行派ARTTBC兑现
    const WALLET_BANKCARD_DARW = 25;  // 艺行派提现到银行卡
    const WALLET_BANKCARD_DARW_REJECT = 26;  // 艺行派提现到银行卡驳回
    const WALLET_ARTBCS_EXCHANGE = 27;  // 艺行派ARTBCS兑现
    const EXCHANGE_TO_ARTBC = 28;  // 兑换为ArTBC

    public function assetType() {
        return $this->belongsTo("App\Model\AssetType", "asset_type", "code");
    }
    public function financeType() {
        return $this->belongsTo("App\Model\FinanceType", "type", "code");
    }
    public function member() {
        return $this->belongsTo("App\Model\Member", "member_id", "id");
    }

}
