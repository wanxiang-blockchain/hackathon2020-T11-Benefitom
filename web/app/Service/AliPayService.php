<?php
/**
 * 支付宝支付服务.
 * User: alan
 * Date: 17-3-15
 * Time: 下午3:42
 */

namespace App\Service;


use App\Model\Account;
use App\Model\AccountFlow;
use App\Model\AlipayLogs;
use App\Model\Finance;
use Illuminate\Support\Facades\Request;

class AliPayService
{
	//建立支付
	public function pay($order_id, $amount, $title, $desc='', $member_id, $back_type=1, $sub_num='',$has_check='')
	{
		$aliPay = app('alipay.mobile');
        $aliPay->setOutTradeNo($order_id);
        $aliPay->setTotalFee($amount);
        $aliPay->setSubject($title);
        $aliPay->setBody($desc);

        if($back_type == 'recharge') {
            $aliPay->setReturnUrl(env('APP_URL') . '/pay/back?back_type='.$back_type.'&sub_num='.$sub_num.'&has_check='.$has_check);
        } else  {
            $aliPay->setReturnUrl(env('APP_URL') . '/subscription/detail/'.$back_type.'?sub_num='.$sub_num.'&has_check='.$has_check.'&o='.$order_id);
        }
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
		// 返回签名后的支付参数给支付宝移动端的SDK。
		return redirect()->to($aliPay->getPayLink());
	}
	//异步通知
	public function webNotify()
	{
        $financeService = new FinanceService();
		if (!app('alipay.web')->verify()) {
			\Log::notice('支付宝异步通知失败.', [
				'data' => \Request::instance()->getContent()
			]);
			return 'fail';
		}
		$pay_log = AlipayLogs::where(['order_id'=>\Request::get('out_trade_no')])->first();
        if($pay_log->status == 1) {
            return 'success';
        }
		switch (\Request::get('trade_status')) {
			case 'TRADE_SUCCESS':
			case 'TRADE_FINISHED':
				\Log::debug('支付宝通知后数据验证成功.', [
					'out_trade_no' => \Request::get('out_trade_no'),
					'trade_no' => \Request::get('trade_no'),
					'data'  => \Request::all(),
				]);
				$pay_log->status = 1;
				$pay_log->paid_at = date('Y-m-d H:i:s');
				$pay_log->content = json_encode(\Request::all());
				$pay_log->save();
				$accountService = new AccountService();
				$account_id = $accountService->getAccountId($pay_log->member_id);
				$accountService->addAsset($account_id, Account::BALANCE, request()->get('total_fee', 0.00), '');
				$financeService->adminRecharge(
                    $pay_log->member_id,
                    Account::BALANCE,
                        Finance::RE_CHANGE
                    ,
                        request()->get('total_fee', 0.00),
                    '',
                    '通过支付宝充值'.request()->get('total_fee', 0.00).'元'
                );
				$accountFlow = new AccountFlow();
				$accountFlow->create_log($pay_log->member_id, $pay_log->money, $pay_log->real_money, '支付宝充值');
				break;
		}

		return 'success';
	}

	//同步通知
	public function webReturn()
	{
		if (!app('alipay.web')->verify()) {
			\Log::notice('支付宝返回查询数据验证失败.', [
				'data' => Request::getQueryString()
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
    public function withdrawal($with_draw_id, $money, $to)
    {
        include '../vendor/latrell/alipay/src/alipay-sdk/aop/AopClient.php';
        include '../vendor/latrell/alipay/src/alipay-sdk/aop/request/AlipayFundTransToaccountTransferRequest.php';
        $aop = new \AopClient();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = '2017022705933296';
        $aop->rsaPrivateKey = 'MIICXQIBAAKBgQC72c/jI8vXzgx14gfndPgQOTEk0NKDfAZSyZHTuhR+cWHAPpQdGeVMULq54TVQJikEKESIQBHPcX+F5+alCriC5TLpIR5Tt2eueTtsvSyx6m87hRlANWwevmDxzxhis2zcCwZ2ZHojgAeOFGvLQXeurD0AKvdD2EvErf3DQRqAnQIDAQABAoGBAKpdv1GkDGJbSCe9aCe9iKDGDdDy5olC+UUp9GyObbnDQMEmwBOogbH14XNlUYGwKjDQCF99o+l8aQhbqm1yTKU0Aw2UXU7kn4ZEh1KADJ7GQcCpJurBAmhVKAOF3egg0+8CqWo/NSdcKnVvhUxEkwK0ZZw5KojvCdnexksLMuWhAkEA49tDWrjKC0YwepXTJjN++AVXc1pHr+El9xGydURzR3D/3n1eeCmdQlPMSipBws6rIGonfjbJ4pOaOv7L1mCCaQJBANMNlbHh6qRuqaEovM+7TGOu7NbFW8zZpu7WpXExIt3iY3/5iwpDpwIHQXtxHkrEbV6SctODJ/NWqH9fshi7nhUCQQCIP1AjK2QFIc9TPcuoiBpgDDGilFVRYfvlpPtlC60zBuq5C5Y1nAyg4KLDpkj0R8gj9dXvrqtBhKkwpbMVWVghAkBy+TWRYBcpWLfD+aNSLyCgNU0EeyNVZ0SPXBNopHHwGkJGFzVtdAlpC3bavnbYGEXUfUdcflinMZA9Q26MFrk5AkBEcck0zIWBu8IQqOztG9RuclOLNQIiApzluYFFxbTTXDPy0U/429yOzLflDe7b7IwHH2f5C5gK0aRwVeJZZM5G';
        $aop->alipayrsaPublicKey='MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB';
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new \AlipayFundTransToaccountTransferRequest ();
        $data = [
            'out_biz_no'=>rand(1000000000000, 9999999999999),
            'payee_type'=>'ALIPAY_LOGONID',
            'payee_account'=>$to,
            'amount'=>$money,
            'payer_show_name'=>'艺行派',
            'payee_real_name'=>'',
            'remark'=>'艺行派现金账户提现'
        ];
        $request->setBizContent(json_encode($data));
        $result = $aop->execute ( $request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        $back_data =  [
            'code'=>$resultCode,
            'msg'=>$result->$responseNode->msg,
            'out_biz_no' => $result->$responseNode->out_biz_no,
            'order_id'=>'',
            'pay_date'=>'',
            'sub_code'=>'',
            'sub_msg'=>''
        ];
        if(!empty($resultCode)&&$resultCode == 10000){
            $back_data['order_id'] = $result->$responseNode->order_id;
            $back_data['pay_date'] = $result->$responseNode->pay_date;
            $msg =  ['code'=>200, 'msg'=>'转账成功'];
        } else {
            $back_data['sub_code'] = $result->$responseNode->sub_code;
            $back_data['sub_msg'] = $result->$responseNode->sub_msg;
            $msg = ['code'=>201, 'msg'=>$result->$responseNode->sub_msg];
        }
        $compact = json_encode(compact('data', 'back_data'));
        \DB::table('with_draws')->where('id', '=', $with_draw_id)->update(['alipay_with_data'=>$compact]);
        return $msg;
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