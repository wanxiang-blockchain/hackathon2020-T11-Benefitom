<?php
/**
 * 微信支付服务.
 * User: alan
 * Date: 17-3-15
 * Time: 下午3:42
 */

namespace App\Service;

use EasyWeChat\Foundation\Application;
use App\Model\Account;
use App\Model\AccountFlow;
use App\Model\AlipayLogs;
use App\Model\Finance;
use EasyWeChat\Payment\Order;
class WechatPayService
{
	//建立支付
	public function jsApi($title, $desc, $order_no, $price, $open_id,$member_id)
	{
	    $options = config('wechat');
	    $app = new Application($options);
        $payment = $app->payment;
        $attributes = [
            'trade_type'       => 'JSAPI',
            'body'             => $desc,
            'detail'           => $title,
            'out_trade_no'     => $order_no,
            'total_fee'        => $price*100, // 单位：分
            'notify_url'       => env('APP_URL').'/wechatNotify',
            'openid'           => $open_id,
        ];
        $order = new Order($attributes);
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
        } else {
            //return [''];
        }
        $poundage = round($price * 0.006, 2);
        $data = [
            'order_id'=>$order_no,
            'member_id'=>$member_id,
            'status'=>0,
            'type'=>2,
            'money'=>$price,
            'poundage'=>$poundage,
            'real_money'=>$price - $poundage //实际到账
        ];
        AlipayLogs::create($data);
        $config = $payment->configForJSSDKPayment($prepayId);
        //$config = $payment->configForPayment($prepayId  );

        return $config;
    }

	//异步通知
	public function wechatNotify()
	{
        $options = config('wechat');
        $app = new Application($options);
        $response = $app->payment->handleNotify(function($notify, $successful){
            // 你的逻辑
            $aliPayService = new AliPayService();
            $financeService = new FinanceService();
            $orderid = $notify->out_trade_no;
            $order = $aliPayService->findItemByOrderId($orderid, 2);
            if(!$order) {
                return 'orderid is not exist!';
            }
            if ($order->status == 1) {
                return true; // 已经支付成功了就不再更新了
            }
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                $order->paid_at = date('Y-m-d H:i:s');
                $order->status = 1;
                $order->content = json_encode($notify);
            } else {
                // 用户支付失败
                $order->status = 3;
            }
            $order->save();
            $total_fee = $notify->total_fee/100;
            if($total_fee != $order->money) {
                return false;
            }

            $accountService = new AccountService();
            $account_id = $accountService->getAccountId($order->member_id);
            $accountService->addAsset($account_id, Account::BALANCE, $total_fee, '');
            $financeService->adminRecharge(
                $order->member_id,
                Account::BALANCE,
                Finance::RE_CHANGE,
                $total_fee,
                    0.00,
                    '通过微信充值'.$total_fee.'元'
            );

            $accountFlow = new AccountFlow();
            $accountFlow->create_log($order->member_id, $total_fee, $order->real_money, '微信充值');
            return true; // 或者错误消息
        });
        $response->send();
	}

}