<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/30
 * Time: 11:01
 */

namespace App\Helpers;

use App\Model\Msg;

include "TopSdk.php";

class SendSmsHelper
{
	protected $appkey;
	protected $secret;

	/**
	 * @param mixed $secret
	 */
	public function setSecret($secret)
	{
		$this->secret = $secret;
	}

	/**
	 * @param mixed $appkey
	 */
	public function setAppkey($appkey)
	{
		$this->appkey = $appkey;
	}

	function __construct($appkey, $secret)
	{
		$this->appkey = $appkey;
		$this->secret = $secret;
	}

	/**
	 * 发送短信
	 * @param $phone 接收的手机号
	 * @param $code  验证码
	 * @return mixed 返回数组
	 */
	public function sendSms($phone, $code)
	{
		$c = new \TopClient($this->appkey, $this->secret);
		//$c->appkey = $this->appkey;
		//$c->secretKey = $this->secret;
		$c->format = "json";//默认是xml
		$req = new \AlibabaAliqinFcSmsNumSendRequest();
		$req->setSmsType("normal");
		$req->setSmsFreeSignName("绍德堂");
		//$param=array('code'=>$code,'number'=>substr($phone,-4,4));
		$param = ['code' => $code];
		$req->setSmsParam(json_encode($param));
		$req->setRecNum($phone);
		$req->setSmsTemplateCode("SMS_11001253");
		$resp = $c->execute($req);

		return $resp;
	}

	/**
	 * 发送通知短信
	 * @param $phone     接收的手机号
	 * @param $name      用户姓名
	 * @param $plantitle 计划名称
	 * @return mixed 返回数组
	 */
	public function sendNotiSms($phone, $name, $plantitle)
	{
		$c = new \TopClient($this->appkey, $this->secret);
		//$c->appkey = $this->appkey;
		//$c->secretKey = $this->secret;
		$c->format = "json";//默认是xml
		$req = new \AlibabaAliqinFcSmsNumSendRequest();
		$req->setSmsType("normal");
		$req->setSmsFreeSignName("绍德堂");
		$param = ['name' => $name, 'plan' => $plantitle];
		//$param=array('code'=>$code);
		$req->setSmsParam(json_encode($param));
		$req->setRecNum($phone);
		$req->setSmsTemplateCode("SMS_11011208");
		$resp = $c->execute($req);

		return $resp;
	}

	/**
	 * 根据
	 * @param $phone
	 * @param $param
	 * @param $templacecode
	 */
	public function sendTemplateSms($phone, $param, $templacecode)
	{
		$c = new \TopClient($this->appkey, $this->secret);
		//$c->appkey = $this->appkey;
		//$c->secretKey = $this->secret;
		$c->format = "json";//默认是xml
		$req = new \AlibabaAliqinFcSmsNumSendRequest();
		$req->setSmsType("normal");
		$req->setSmsFreeSignName("绍德堂");
		//$param=array('code'=>$code);
		$req->setSmsParam(json_encode($param));
		$req->setRecNum($phone);
		$req->setSmsTemplateCode($templacecode);
		$resp = $c->execute($req);

		\Log::info(json_encode($resp));

		return $resp;
	}

	public static function withdrawNotice($phone)
	{
		$send = new SendSmsHelper(config('sms.sms_appkey'), config('sms.sms_secret'));


		$param = [
			'user' => $phone,
			'time' => date('Y-m-d H:i:s'),
			'phone' => '400-665-5296'
		];

		$re_send = $send->sendTemplateSms($phone, $param, 'SMS_94090002');

		Msg::record($param, $phone, 'SMS_94090002');

		return $re_send;
	}


	public static function deliveryNotice($phone, $people)
	{
		$send = new SendSmsHelper(config('sms.sms_appkey'), config('sms.sms_secret'));

		// 尊敬的用户${user}，您于${time}在绍德堂用户管理中心发起的提货申请提交成功，收货人${people}，如非本人操作，请于24小时内联系绍德堂客服${service}。
		$param = [
			'user' => $phone,
			'time' => date('Y-m-d H:i:s'),
			'people' => $people,
			'service' => '400-665-5296'
		];

		$re_send = $send->sendTemplateSms($phone, $param, 'SMS_94120035');

		Msg::record($param, $phone, 'SMS_94120035');

		return $re_send;
	}
}