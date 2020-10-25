<?php

namespace App\Http\Controllers\Front;

use App\Model\Artbc\Wxuser;
use App\Model\Member;
use App\Service\AliPaySdkService;
use App\Service\AliPayService;
use App\Service\SsoService;
use App\Service\WechatPayService;
use App\Utils\UrlUtil;
use App\Utils\WxPayUtil;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

/**
 * Class PayController
 * @package App\Http\Controllers\Front
 */
class PayController extends Controller
{
	/**
	 * 1、判断是否为微信，如是引导打开浏览器
	 * 2、TODO 判断手机端还是pc端，启动相应支付地址
	 * @desc getRecharge
	 * @param AliPaySdkService $aliPayService
	 * @param Request          $request
	 * @return string|\提交表单HTML文本
	 */
    public function getRecharge(AliPaySdkService $aliPayService, Request $request) {
	    session_start();
	    Log::info('get', [
	        'get' => $_GET
	    ]);
	    if ($request->get('paytype') == 1){
		    $back_type = $request->get('back_type', 'recharge');
		    $sub_num = $request->get('sub_num', '');
		    $amount = $request->get('rechangeAmount');
		    $amount *= 3;
		    $has_check = $request->get('has_check');
		    $this->validate($request, [
			    'rechangeAmount'=>['required', 'regex:/^(0|[1-9][0-9]{0,9})(\.[0-9]{1,2})?$/']
		    ]);
		    if (UrlUtil::isWeChatBrowser($request) || isset($_GET['hehe'])) {
			    return redirect()->to('/pay/wxguide?ticket=' . $_COOKIE['ticket'] . '&' . http_build_query($_GET));
		    }
		    $member_id = $request->user('front')->id;
		    return $aliPayService->pay(order_id('CZ'), $amount, '平台购买ARTTBC', '购买ARTTBC'.$amount.'元', $member_id, $back_type, $sub_num,$has_check);
	    }else{
	    	// todo 1、has openid
		    $member = Member::current();
		    $wxuser = session('wechat.oauth_user');
		    Log::info("openid: ", [
		    	'wechat.oauth_user' => $wxuser,
			    'session' => $_SESSION
		    ]);
		    $config = config('wechat');
		    $config['oauth']['callback'] = '/pay/getRecharge';
		    $app = new Application($config);
		    if ( $request->has('rechangeAmount')){
			    $_SESSION['rechangeAmount'] =  $request->get('rechangeAmount');
			    $amount =  $request->get('rechangeAmount');
		    } else{
		    	$amount = session('rechangeAmount');
		    }
		    if ($wxuser == '' || empty($wxuser)) {
//			    $_SESSION['target_url'] = '/?action=getRecharge&rechangeAmount=' . $request->get('rechangeAmount');
			    // todo code=001GWGy62NrzpL0zgZw62bdpy62GWGyi&state=6bd76217518f23d8576524156951dded 没走对
			    $code = $request->get('code');
			    if (empty($code)) {
				    return $app->oauth->redirect();
			    }
			    $access_token = $app->oauth->getAccessToken($code);
			    Log::info('access_token', [
				    $access_token
			    ]);
			    if (!$access_token) {
				    return '授权失败，请重新授权或联系管理员';
			    }
			    $wxuser = $app->oauth->user($access_token);
			    Log::info('wxuser', [
			        'wxuser' => $wxuser
			    ]);
		    }
		    $openid = $wxuser->getId();
		    Log::info("openid", [
		    	'openid' => $openid,
			    'session' => $_SESSION
		    ]);
		    $model = Wxuser::fetch($openid);
		    $insert = array(
			    'openid' => strval($openid),
			    'unionid' => (empty($wxuser->getOriginal()['unionid'])) ? "" : $wxuser->getOriginal()['unionid'],
			    'appid' => config('wechat.app_id'),
			    'name' => '',
			    'nickname' => '',
			    'headimg' => '',
			    'member_id' => $member->id
		    );
		    Log::info('insert wxuser', $insert);
		    if (empty($model)){
			    Wxuser::create($insert);
		    }else{
			    $model->fill($insert);
			    $model->save();
		    }
		    if (empty($amount)){
		        $amount = $_SESSION['rechangeAmount'];
		    }
			$data = WxPayUtil::recharge($amount, $openid);
		    return view('front.member.center.wxrecharge', compact('data'));
	    }
    }

    //回调异步通知
    public function notify(AliPaySdkService $aliPayService, Request $request) {
       return $aliPayService->webNotify($request);
    }

    //同步跳转
    public function back(AliPaySdkService $aliPayService, Request $request)
    {
        $aliPayService->webReturn($request);
        return view('front.member.center.rechargesuccess', ['amount'=>$request->get('total_amount')]);
    }

	/**
	 * 判断是否为微信端:
	 *  是：展示引导页
	 *  否：获取登录信息
	 *      登录成功：跳充值页
	 *      登录失败：跳登录页
	 *
	 * @desc wxguide
	 * @param Request $request
	 */
    public function wxguide(Request $request)
    {
    	if (UrlUtil::isWeChatBrowser($request) || isset($_GET['hehe'])) {
    		return view('front.member.center.wxguide');
	    }

	    // 获取登录信息
	    $ticket = $request->get('ticket');

    	\Log::debug($_GET);
    	\Log::debug($ticket);

	    empty($ticket) && $ticket = isset($_COOKIE['ticket']) ? $_COOKIE['ticket'] : null;

	    \Log::debug($ticket);

	    // 在所有页面，如果无ticket或者是假ticket，不过该中间件
	    if (empty($ticket) || ($data = SsoService::verify($ticket)) === false) {
	    	return redirect()->to(UrlUtil::ssoMLoginUrl());
	    }

	    $ticket = $data['ticket'];
	    $uid = $data['uid'];
	    $expries = $data['expires'];

	    //	    // session 存不住？用此方法存的session作response时才会存入$_SESSION?
	    SsoService::setCookie('ticket', $ticket, $expries);
	    SsoService::setCookie('uid', $uid, $expries);

	    return redirect()->to('member/recharge');

    }

}
