<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/6/21
 * Time: 上午11:08
 */

namespace App\Service;


use App\Model\Account;
use App\Model\AccountFlow;
use App\Model\AlipayLogs;
use App\Model\Btshop\BlockAssetLog;
use App\Model\Btshop\BlockAssetType;
use App\Model\Finance;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class AliPaySdkService
{

	public $app;

	public $aop;

	public function __construct(Application $app)
	{
		$this->app = $app;
		include ($this->app->basePath() . '/extensions/alipay-sdk-PHP-20170615110533/AopSdk.php');
		include $this->app->basePath() . '/extensions/alipay-sdk-PHP-20170615110533/aop/AopClient.php';
		include $this->app->basePath() . '/extensions/alipay-sdk-PHP-20170615110533/aop/request/AlipayTradeWapPayRequest.php';
		$aop = new \AopClient ();
		$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
		$aop->appId = '2017062007532504';
		$aop->rsaPrivateKey = 'MIIEpAIBAAKCAQEA25/iTjVXv27+6Ny4eX0SE+9Iw+xTTpbwnQ9NLJEm/HJJQN53RcEzWQ/CQmn03v0QuWCFATpZYuXJ7cDSqhgBq6sbCBSu38XpeYO+q+gFh3aOGUZwoqAG7znmueraN0ZcGd+PqqBngqv8xtipYIMW0wnf8/CQP3NYDDsZdle/si+RUWlXxnhPN9tEn2KWac2A71g1JKJYmqyz6fFkKojtXhd3vkAWAONlfNKoOto+eUp4h9f8jibhxPMe14eo7psmSvGsKyCgKzST3w1g5GJAMwfbjWZyjDmIJbsjdXM5oS4s0u/LQzUQu6NIENMXLlxwbg5VDDq/hJo03eR7sVXavwIDAQABAoIBAQCYoNdcNLQDhPbAC8xkGag7lFqEVjNa9YjIDb87TrDCVgZUf4ZyQp8VsN722r3jTgmWWt50CQHfugQIJ63/qWlXzTSe/Ai/2MG1wfn7a+aTyq9cH1jdm8rV6+5NnEoEifAtxvEHyivFYxLredzUBqYRCdbKp48HmXhCH7xFQxwkjqHVIsNM8zunGXOvvmSRI/oQJgcTlcY2nBrPG3mrnMCMdpAUPXLWndDZQSYrllX5oks0Ycs9eKqVjsKMkRbKEk08xCcrdYCYQy0BPG9p6XaOlFI1BWC+xNMgMuEEkbrvJgJMtRKEMsSf/Xvwwv1yrRVkoqIBQyY8n8dNCKNy05+ZAoGBAP5IO7HYrNhiQX0O4pFwbSSVG/NZp51d3IExrzdZos7P5OIrbvJyp4HR+o3Oc88QYb6g0O3sj8A1INm4++6Mi/vN5EwFFwKmuoUSenigXgmNmBG15AzuRQFGVnEn/nDU23J/DbwZxmxfK8hBVRzTJj3eFOOS0NblP2XE1s1As+I9AoGBAN0btmCtK1XFWD9nnSOdpbQe7I12ZIK5Re9aZRky6YwR+KoS330HATSVjuJURdNgS+aW+dbQpfmxLdDLcyEekGGSdEDzGoRMV88F5ef9AmgTUCOK4KR318V3RInheeQPTRT/pb2ypsuC86+Y6KSZ9aF9oi6gYzeUT0rXVdD0bGyrAoGBAKVcXg2i5Yx2ITZthqOkJCYFHb8cT6dD0dDmeZaaIQkzIxp9ulHKg+olnjW3y5IdiiMIu7Xt//Gz2yAbbyJLngDrfQp+yN79hBBj2uc1CtJVlU33Jk3OaWuRzUcbBhlkIy2LOGhZrrLtFTDOY8Yx/g1/rb/sywjk3lyVLEcgS5rVAoGASTRdo5Bx4ond99AURLBzJjl+1C84g9dXgjGJe1hR3qAslhJ9j+V7zq8diW2hWKRvPL8LpMWbzhmNM63IRlTaNJPKjZ5vTxH/mHk5bx9jZpXOWJYoXguXkrc0J/pQ4uL2AFfLxgyaEJMgnyifDbAzlO4Ffi536vTdc8frj6z9INUCgYAQ9Cl4KJ+gDAmV8HM2e4Re/8Fl16r4cHV3jhLKeMkNqrawUfj6FJF2QKujIPi/1O1tklF+jZnWNyfbgQIbaRlWeGJaGTRPD4W9UIyC35u6zPsk+9iNrmZyz/ZF7rRsCpZdO2TX8r/NLw7J+tX+jzyv3p5jJXSe9o0fZHn3WmXS1Q==';
		$aop->alipayrsaPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAioXhjPV4NSxpqQgy253nMiwevSWewi/yHUTYDPoCH/sDwG1yA4dl8lS10uY7/KtadZkS8uMAYVI0xhly0Ej+NkGK7qQNazI8xZxnQzMDjpKHgDPnwhPdeiYjkGXhor9meCHqOJfCrIFM1Egq/q1v0aLgr5JGI7dNuPVcehyDBV40u6Rph4LLaJawWpcHvFNAgnyAM8+e7KUV0KIMpia17jw3OGNahH3kE0z0l9J1UrZTh7z70mL8g70YX7V/3lTQkxh1Bw+XIdw88eUXL6XJIMRq6ZaOdg7fNkDh4utfLSII+/78l1mMReZmiqG3/3kADNsFi0a1hdQYoYc+6SshPQIDAQAB';
//		$aop->alipayrsaPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA25/iTjVXv27+6Ny4eX0SE+9Iw+xTTpbwnQ9NLJEm/HJJQN53RcEzWQ/CQmn03v0QuWCFATpZYuXJ7cDSqhgBq6sbCBSu38XpeYO+q+gFh3aOGUZwoqAG7znmueraN0ZcGd+PqqBngqv8xtipYIMW0wnf8/CQP3NYDDsZdle/si+RUWlXxnhPN9tEn2KWac2A71g1JKJYmqyz6fFkKojtXhd3vkAWAONlfNKoOto+eUp4h9f8jibhxPMe14eo7psmSvGsKyCgKzST3w1g5GJAMwfbjWZyjDmIJbsjdXM5oS4s0u/LQzUQu6NIENMXLlxwbg5VDDq/hJo03eR7sVXavwIDAQAB';
		$aop->apiVersion = '1.0';
//		$aop->postCharset='utf-8';
//		$aop->format='json';
		$aop->signType='RSA2';
		$this->aop = $aop;
	}

	public function pay($order_id, $amount, $title, $body, $member_id, $back_type=1, $sub_num='',$has_check='')
	{
		\Log::debug($this->app->basePath() . '/extensions/alipay-sdk-PHP-20170615110533/aop/AopClient.php');

		$request = new \AlipayTradeWapPayRequest ();
		if($back_type == 'recharge') {
		    $returnUrl = env('APP_URL') . '/pay/back?back_type='.$back_type.'&sub_num='.$sub_num.'&has_check='.$has_check;
		} else  {
		    $returnUrl = env('APP_URL') . '/subscription/detail/'.$back_type.'?sub_num='.$sub_num.'&has_check='.$has_check.'&o='.$order_id;
		}
		$notifyUrl = env('APP_URL') . '/pay/notify';
		$request->setReturnUrl($returnUrl);
		$request->setNotifyUrl($notifyUrl);
		$data = [
			'body' => $body,
			'subject' => $title,
			'out_trade_no' => $order_id,
			'timeout_express' => '90m',
			'total_amount' => $amount,
			'product_code' => 'T000000001',
			'disable_pay_channels' => 'creditCard,creditCardExpress,creditCardCartoon,credit_group'
		];
		$request->setBizContent(json_encode($data));

		if(time() < strtotime('2017-12-31 23:59:59')) {
			$fee = 0.0055;
		} else {
			$fee = 0.006;
		}
		$poundage = round($amount * $fee, 2);
		$data = [
			'order_id'=>$order_id,
			'member_id'=>$member_id,
			'status'=>0,
			'money'=>$amount,
			'poundage'=>$poundage,
			'real_money'=>$amount - $poundage //实际到账
		];
		AlipayLogs::create($data);
		return $this->aop->pageExecute ( $request);
	}

	//异步通知
	public function webNotify(Request $request)
	{
		DB::beginTransaction();
		try{

//			$financeService = new FinanceService();
			if (!$this->aop->rsaCheckV1($_POST, '', $this->aop->signType)) {
				\Log::notice('支付宝异步通知失败.', [
					'data' => $_POST,
				]);
				return 'fail';
			}
			$order_id = \Request::get('out_trade_no');
			$pay_log = AlipayLogs::where(['order_id'=>$order_id])->first();
			if($pay_log->status == 1) {
				return 'success';
			}
			switch (\Request::get('trade_status')) {
				case 'TRADE_SUCCESS':
				case 'TRADE_FINISHED':
					\Log::debug('支付宝通知后数据验证成功.', [
						'out_trade_no' => \Request::get('out_trade_no'),
						'trade_no' => \Request::get('trade_no'),
						'data'  => $_POST,
					]);
					$pay_log->status = 1;
					$pay_log->paid_at = date('Y-m-d H:i:s');
					$pay_log->content = json_encode(\Request::all());
					$pay_log->save();


                // todo 改为添加 ARTTBC
                    $amount = request()->get('total_amount', 0.00);
                    $coinAmount = round($amount/3, 2);
                    BlockAssetLog::record($pay_log->member_id, BlockAssetType::CODE_ARTTBC, $coinAmount,
                        BlockAssetLog::ALIPAY_BUY_ARTTBC, '支付宝购买' . $coinAmount, $order_id);
//					$accountService = new AccountService();
//					$account_id = $accountService->getAccountId($pay_log->member_id);
//					$accountService->addAsset($account_id, Account::BALANCE, request()->get('total_amount', 0.00), '');
//					$financeService->adminRecharge(
//						$pay_log->member_id,
//						Account::BALANCE,
//						Finance::RE_CHANGE
//						,
//						request()->get('total_amount', 0.00),
//						'',  //  资产数量
//						'通过支付宝充值'.request()->get('total_amount', 0.00).'元'
//					);
					$accountFlow = new AccountFlow();
					$accountFlow->create_log($pay_log->member_id, $pay_log->money, $pay_log->real_money, '支付宝购买ARTTBC');
					break;
			}
			DB::commit();
		} catch (Exception $e){
			\Log::debug($e->getTraceAsString());
			return 'fail';
			DB::rollBack();
		}

		return 'success';
	}

	//同步通知
	public function webReturn(Request $request)
	{
		if (!$this->aop->rsaCheckV1($_GET, '', $this->aop->signType)) {
//			throw new Exception('支付宝验签失败');
			\Log::notice('支付宝返回查询数据验证失败.', [
				'data' => \Request::getQueryString()
			]);
			return 'fail';
		}
		switch (\Request::get('trade_status')) {
			case 'TRADE_SUCCESS':
			case 'TRADE_FINISHED':
				\Log::debug('支付宝返回查询数据验证成功.', [
					'out_trade_no' =>  \Request::get('out_trade_no'),
					'trade_no' =>  \Request::get('trade_no')
				]);
				break;
		}

		return 'return';
	}

	//支付宝提现
	public function withdrawal($order_no, $money, $to, $payee_real_name)
	{
//		include '../vendor/latrell/alipay/src/alipay-sdk/aop/AopClient.php';
//		include '../vendor/latrell/alipay/src/alipay-sdk/aop/request/AlipayFundTransToaccountTransferRequest.php';
		$aop = $this->aop;
		$aop->apiVersion = '1.0';
		$aop->postCharset='UTF-8';
		$aop->format='json';
		$request = new \AlipayFundTransToaccountTransferRequest ();
		if (env('APP_ENV', 'prod') !== 'prod'){
		    $money = 1;
        }
		$data = [
			'out_biz_no'=>$order_no,
			'payee_type'=>'ALIPAY_LOGONID',
			'payee_account'=>$to,
			'amount' => round($money, 2),
			'payer_show_name'=>'艺行派',
			'payee_real_name'=> $payee_real_name,
			'remark'=>'艺行派现金账户提现'
		];
		\Log::debug($data);
		$request->setBizContent(json_encode($data));
		$result = $aop->execute ( $request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		return $result->$responseNode;

	}

	public function orderQuery($order_no, $order_id)
    {
        $aop = $this->aop;
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new \AlipayFundTransOrderQueryRequest ();
        $data = [
            'out_biz_no' => $order_no,
            'order_id' => $order_id
        ];
        $request->setBizContent(json_encode($data));
        $result = $aop->execute ( $request);
        Log::info(__FUNCTION__, [
            'data' => $data,
            'result' => json_encode($result)
        ]);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        return empty($resultCode) && $resultCode == 10000;

    }
	//根据订单 id 获取支付信息
	public function findItemByOrderId($order_id, $type = 1)
	{
		$where = [
			'type'=>$type,
			'order_id'=>$order_id,
		];
		return AlipayLogs::where($where)->first();
	}
}