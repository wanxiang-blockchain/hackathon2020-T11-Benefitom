<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-27
 * Time: 17:24
 */

namespace App\Http\Controllers\Front;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\AlipayLogs;
use App\Model\BlockTransferLog;
use App\Model\Btshop\AlipayDraw;
use App\Model\Btshop\BankDraw;
use App\Model\Btshop\BtshopOrder;
use App\Model\Btshop\BtshopProduct;
use App\Model\Btshop\SuperMember;
use App\Model\Member;
use App\Utils\DisVerify;

class WalletController extends Controller
{
    public function sum($tmptk)
    {
        try{
            $member = DisVerify::verifyTk($tmptk);
            if (empty($member) || !SuperMember::isSuper($member->phone)){
                throw new TradeException('不要跳');
            }
            // 今天之前的
            $total_sum_bt = BtshopOrder::buySumPay(BtshopProduct::PAYTYPE_BT);
            $total_sum_artbc = BtshopOrder::buySumPay(BtshopProduct::PAYTYPE_ARTBC);
            $total_sum_rmb = BtshopOrder::buySumPay(BtshopProduct::PAYTYPE_RMN);
            $total_sum_artbcs = BtshopOrder::buySumPay(BtshopProduct::PAYTYPE_ARTBCS);
            $total_member_sum = Member::totalCount();
            // 累计alipay_draws
            $total_alipay_draw_sum = AlipayDraw::totalSum();
            // 累计充值
            $total_ali_recharge_sum = AlipayLogs::totalSum();

            // 今天的
            $today_bt_amount = BtshopOrder::todayPayAmount(BtshopProduct::PAYTYPE_BT);
            $today_artbc_amount = BtshopOrder::todayPayAmount(BtshopProduct::PAYTYPE_ARTBC);
            $today_rmb_amount = BtshopOrder::todayPayAmount(BtshopProduct::PAYTYPE_RMN);
            $today_artbcs_amount = BtshopOrder::todayPayAmount(BtshopProduct::PAYTYPE_ARTBCS);
            $today_member_sum = Member::todayCount();
            $today_alipay_draw_sum = AlipayDraw::todaySum();
            // 今日充值
            $today_ali_recharge_sum = AlipayLogs::todaySum();

            // 2019-03-02 至今
            //ARTTBC 转账总量 650700
            //获得转入 ARTTBC 用户银行卡提现金额： 286810.00
            //          支付宝提现金额： 473150.00
            //获得转入 ARTTBC 用户ARTTBC报单金额 218
//            $arttbc_transfer_total = BlockTransferLog::where('created_at', '>', '2019-03-02')
//                ->where('code', '300002')
//                ->sum('amount');
//            $arttbc_transfer_ids = BlockTransferLog::where('created_at', '>', '2019-03-02')
//                ->where('code', '300002')
//                ->select('member_id')
//                ->groupBy('member_id')
//                ->toArray()->get();
//            $arttbc_transfer_ids = array_column($arttbc_transfer_ids, 'member_id');
//            $arttbc_transfer_bank_draws = BankDraw::where('created_at', '>', '2019-03-02')
//                ->whereIn('member_id', $arttbc_transfer_ids)
//                ->sum('amount');
//            $arttbc_transfer_alipay_draws = AlipayDraw::where('created_at', '>', '2019-03-02')
//                ->whereIn('member_id', $arttbc_transfer_ids)
//                ->sum('amount');
//            $arttbc_transfer_btshop_orders = BtshopOrder::where('created_at', '>', '2019-03-02')
//                ->whereIn('member_id', $arttbc_transfer_ids)
//                ->where('stat', 1)
//                ->where('paytype', 2)
//                ->sum('amount');

            return view('front.wallet.sum', compact('total_sum_bt', 'total_sum_artbc', 'total_sum_rmb',
                'today_bt_amount', 'today_artbc_amount', 'today_rmb_amount', 'total_member_sum',
                'today_member_sum', 'total_alipay_draw_sum', 'today_alipay_draw_sum',
                'total_ali_recharge_sum', 'today_ali_recharge_sum', 'today_artbcs_amount', 'total_sum_artbcs'));
        }catch (TradeException $e){
            echo ($e->getMessage());
        }
    }
}