<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/7/6
 * Time: 上午8:56
 */

namespace App\Utils;


use App\Model\AlipayLogs;
use App\Model\Member;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WxPayUtil
{
	public static function recharge($amount, $openid)
	{
		if(!is_weixin()) {
			return ['code'=>201, 'data'=>'请在微信公众号中登录'];
		}
		Log::info('amount', [
			'amount' => $amount,
			'openid' => $openid
		]);
		if($amount <= 0) {
			return ['code'=>201, 'data'=>'请输入正确的金额'];
		}
		$order_no = order_id('WX');
		// 获取open_id
		$member = Member::current();
		$options = config('wechat');
		// 生成预订单
		$app = new Application($options);
		$payment = $app->payment;
		/**
		 * var Overtrue\Socialite\User
		 */
		$attributes = [
			'trade_type'       => 'JSAPI',
			'body'             => '平台账户充值' . $amount . '元',
			'detail'           => '平台账户充值充值' . $amount . '元',
			'out_trade_no'     => $order_no,
			'notify_url'       => 'https://dis.yigongpan.com/wxpay/callback',
			// 如果获取 openid
			'openid'           => $openid,
			'total_fee'        => $amount * 100, // 微信是以分为单位
			'limit_pay'        => 'no_credit'
		];
		$order = new Order($attributes);
		$result = $payment->prepare($order);
		Log::info(json_encode($result));
		if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
			$prepayId = $result->prepay_id;
		} else {
			return ['code'=>201, 'data'=>'预支付失败'];
		}

		// 创建订单记录
		$data = [
			'order_id'=>$order_no,
			'member_id'=>$member->id,
			'status'=>0,
			'money'=>$amount,
			'poundage'=> 0 ,
			'real_money'=>$amount, //实际到账
			'type' => AlipayLogs::TYPE_WX,
		];
		AlipayLogs::create($data);

		$config = $payment->configForJSSDKPayment($prepayId);

		\Log::debug('wx_order data : ' . json_encode($data));
		\Log::debug('config for wx : ' . json_encode($config));

		return ['code'=>200, 'data'=>$config];
	}
}