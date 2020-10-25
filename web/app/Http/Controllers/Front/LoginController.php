<?php

namespace App\Http\Controllers\Front;

use App\Model\Account;
use App\Service\SmsService;
use App\Service\SsoService;
use App\Service\ValidatorService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Member;
use Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Session\Store as Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Hashing\BcryptHasher as Hasher;

class LoginController extends Controller
{
    protected $validatorService;
    protected $request;

    public function __construct(ValidatorService $_validator)
    {
        $this->validatorService = $_validator;
    }

    //登录
    public function login(Request $request) {
        $data = $request->all();

        $validator = $this->validatorService->checkValidator([
            'phone'    => 'digits:11|required',
            'password' => 'required'
        ], $data);
        if($validator['code'] != 200) {
            return $validator;
        }
        if (openssl_private_decrypt(base64_decode($request->get('password')),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $data['password'] = $decrypted;
        } else {
            die('');
        }
        $member = Member::where([
                'phone'    => $request->input('phone')
            ])->first();
        if ($member) {
            if($member->is_lock) {
                return ['code'=>201, 'data'=>'登录失败,您的账号已禁用,请联系管理员'];
            }
            $agreement = $request->get('agreement') ? true : false;
            if (SsoService::login($request->input('phone'), $data['password'])) {
                \Log::debug(__CLASS__ . __LINE__, [
                    'msg' => 'login success',
                    'member' => $member,
                    'agreement' => $agreement,
                    'guard-login' => Auth::guard('front')->login($member, $agreement)
                 ]);
                Auth::guard('front')->login($member, $agreement);
                return ['code'=>200, 'data'=>'恭喜您,登录成功'];
            }
        }
        return ['code'=>201, 'data'=>'账号和密码不匹配'];
    }

    public function logSuccess()
    {
        //return redirect('/');
        return view('front.member.successmessage', ['message'=>'恭喜您,登录成功']);
    }

    public function logout()
    {
        Auth::guard('front')->logout();
        return redirect('/');
    }

    public function getLogin(Request $request)
    {
        $prev_action = urldecode($request->get('prev_action', '/'));
        if(Auth::guard('front')->check()) {
            return redirect($prev_action);
        }
        \Log::debug(__CLASS__ . __LINE__);
        $key  = trim(file_get_contents("../rsa_1024_pub.pem"));
        Auth::guard('front')->logout();
        return view("front.member.login", compact('key', 'prev_action'));
    }

	/**
	 * @desc inviteReg
	 * @param $code 邀请码
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
    public function inviteReg($code)
    {
	    if(!Auth::guard('front')->check()) {
		    $key  = trim(file_get_contents("../rsa_1024_pub.pem"));
		    Auth::guard('front')->logout();
		    return view("front.member.login",compact('key'));
	    }
	    \Log::debug('邀请码：' . $code);
	    return redirect('/');
    }

    //找回密码第一步骤
    public function getOneForget() {
        return view('front.member.forget.one');
    }

    public function oneForget(Request $request)
    {
        $validator = $this->validatorService->checkValidator([
            'captcha'=>'required',
            'phone'=>'required|regex:/^1[34578]\d{9}$/',
        ], $request->all());
        if($validator['code'] != 200) {
            return $validator;
        }
        if(Auth::guard('front')->check()) {
            $member_phone = $request->user('front')->phone;
            if ($member_phone) {
                if ($member_phone != $request->get('phone')) {
                    return ['code' => 202, 'data' => '请填写当前登录的手机号'];
                }
            }
        }
        if(!Member::where(['phone'=>$request->get('phone')])->first()) {
            return ['code'=>202, 'data'=>'手机号未注册'];
        }
        if(!captcha_check($request->get('captcha'))) {
            return ['code'=>201, 'data'=>'请输入正确的图文验证码'];
        }
        session()->put('oneForget', ['phone'=>$request->get('phone')]);
        return ['code'=>200, 'data'=>''];
    }

    public function getTwoForget(Request $request) {
        $key  = trim(file_get_contents("../rsa_1024_pub.pem"));
        return view('front.member.forget.two', ['key'=>$key, 'phone'=>session()->get('oneForget')['phone']]);
    }

    public function twoForget(Request $request, SmsService $service)
    {
        $data = $request->all();
        $decrypted = "";
        if (openssl_private_decrypt(base64_decode($request->get('password')),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $data['password'] = $decrypted;
            openssl_private_decrypt(base64_decode($request->get('password_confirmation')),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")));
            $data['password_confirmation'] = $decrypted;
            openssl_private_decrypt(base64_decode($data['verificationCode']),$decrypted2, trim(file_get_contents("../rsa_1024_priv.pem")));
            $data['verificationCode'] = $decrypted2;
        } else {
            die('');
        }
        $validator = $this->validatorService->checkValidator([
            'phone'    => 'required|digits:11',
            'password'   => 'required|confirmed|between:6,20',
        ], $data);
        if($validator['code'] != 200) {
            return $validator;
        }
        $validator = $service->verifyCode($data['verificationCode'], $data['phone']);
        if($validator['code'] != 200) {
            return $validator;
        }
        // 对接sso 重置密码
	    $ret = SsoService::resetPwd($request->get('phone'), $data['password']);

        if ($ret['code'] != 0) {
        	return $ret;
        }

//        $member = Member::where(['phone'=>$request->get('phone')])->first();
//        $member->password = Hash::make($data['password']);
//
//        $member->save();
        $code = session('verify_code');
        $code['valid'] = 0;
        session(['verify_code'=>$code]);
        Auth::guard('front')->logout();
        return ['code'=>200, 'data'=>''];
    }

    public function getThreeForget() {
        return view('front.member.forget.three');
    }

    //注册页面
    public function getRegister(Request $request) {
        if(Auth::guard('front')->check()) {
            return redirect('/');
        }
        $key  = trim(file_get_contents("../rsa_1024_pub.pem"));
        $invite_member = $request->get('invite_member', '');
        return view('front.member.register', ['key'=>$key, 'invite_member' => $invite_member]);
    }

    //生成图形验证码
    public function captcha()
    {
        return ['code'=>200, 'data'=>captcha_src()];
    }

    public function sendSms(SmsService $service, Request $request){
        $validator = $this->validatorService->checkValidator([
            'phone'=>'required|regex:/^1[34578]\d{9}$/',
        ], $request->all());
        if($validator['code'] != 200) {
            return $validator;
        }
        if(Member::where(['phone'=>$request->get('phone')])->first()) {
            return ['code'=>201, 'data'=>'该手机号已注册'];
        }
        if($request->has('captcha')) {
            if (!captcha_check($request->get('captcha'), false)) {
           //     return ['code' => 201, 'data' => '请输入正确的图文验证码'];
            }
        }
        $code = session('verify_code');
        if(isset($code['valid']) && $code['valid'] && $code['phone'] == $request->get('phone')) {
            if ($code['expired_time'] > time()) {
                return ['code' => 203, 'data' => '验证码未过期,请稍后再试', '_data' => $code['expired_time'] - time()];
            }
        }
        if(isset($code['time'])){
            if ($code['time'] >= $service->time && (time() - $code['expired_time']) <= $service->limit) {
                $has_second = intval($service->limit/60) - intval((time() - $code['expired_time'])/60);
                return ['code' => 204, 'data' => '验证码获取频繁,请'.$has_second.'分钟后获取'];
            } elseif ($code['time'] >= $service->time && (time() - $code['expired_time']) > $service->limit) {
                $code['time'] = 0;
                session(['verify_code' => $code]);
            }
        }
        $ret = SsoService::sms($request->get('phone'));
        if($ret !== false) {
            return ['code'=>200, 'data'=>$service->valid_time];
        }
        return ['code'=>202, 'data'=>'发送失败'];
    }
    public function sendSmsNoAuth(SmsService $service, Request $request){
        $validator = $this->validatorService->checkValidator([
            'phone'=>'required|regex:/^1[34578]\d{9}$/',
        ], $request->all());
        if($validator['code'] != 200) {
            return $validator;
        }
         if($request->has('captcha')) {
            if (!captcha_check($request->get('captcha'), false)) {
                return ['code' => 201, 'data' => '请输入正确的图文验证码'];
            }
        }
        $code = session('verify_code');
        if(isset($code['valid']) && $code['valid'] && $code['phone'] == $request->get('phone')) {
            if ($code['expired_time'] > time()) {
                return ['code' => 203, 'data' => '验证码未过期,请稍后再试', '_data' => $code['expired_time'] - time()];
            }
        }
        if(isset($code['time'])){
            if ($code['time'] >= $service->time && (time() - $code['expired_time']) <= $service->limit) {
                $has_second = intval($service->limit/60) - intval((time() - $code['expired_time'])/60);
                return ['code' => 204, 'data' => '验证码获取频繁,请'.$has_second.'分钟后获取'];
            } elseif ($code['time'] >= $service->time && (time() - $code['expired_time']) > $service->limit) {
                $code['time'] = 0;
                session(['verify_code' => $code]);
            }
        }
        $ret = $service->sendMessage($request->get('phone'));
        if($ret) {
            return ['code'=>200, 'data'=>$service->valid_time];
        }
        return ['code'=>202, 'data'=>'发送失败'];
    }

    //注册
    public function postRegister(Request $request, Member $member) {
        $data = $request->all();
        if (openssl_private_decrypt(base64_decode($request->get('password')),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $data['password'] = $decrypted;
        } else {
            die('');
        }
        if(openssl_private_decrypt(base64_decode($request->get('verificationCode')),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))){
            $data['verificationCode'] = $decrypted;
        } else {
            die('');
        }
        if(openssl_private_decrypt(base64_decode($request->get('tradePassword')),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))){
            $data['tradePassword'] = $decrypted;
        } else {
            die('');
        }
        if(openssl_private_decrypt(base64_decode($request->get('againTradePassword')),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))){
            $data['againTradePassword'] = $decrypted;
        } else {
            die('');
        }

        $validator = $this->validatorService->checkValidator([
            'phone'    => 'required|digits:11',
            'password'   => 'required|alpha_num|between:6,20',
            'tradePassword'=>'required|alpha_num|between:6,20',
            'againTradePassword'=>'required|alpha_num|between:6,20',
        ], $data);
        if($validator['code'] != 200) {
            return $validator;
        }
        if($data['tradePassword'] != $data['againTradePassword']) {
            return ['code'=>201, 'data'=>'请输入两次相同的交易密码'];
        }
        if($data['tradePassword'] == $data['password']) {
            return ['code'=>201, 'data'=>'交易密码和登录密码不能使用同一个'];
        }
        if(Member::where(['phone'=>$request->get('phone')])->first()) {
            return ['code'=>201, 'data'=>'该手机号已注册，请登录！'];
        }
        if($request->get('agreement') != 'on') {
            return ['code'=>201, 'data'=>'请先勾选我已阅读并同意《北京绍德堂文化有限公司
艺术收藏品数字化认购与交易协议》'];
        }

	    if (!captcha_check($request->get('captcha'))) {
		    //return ['code' => 201, 'data' => '请输入正确的图文验证码'];
	    }

	    // 向sso注册
	    $ret = SsoService::reg($request->get('phone'), $data['password'], $data['verificationCode']);
	    if ( $ret['code'] != 0)
	    {
		    return ['code' => 201, 'data' => $ret['data']];
	    }

        $data['phone'] = $request->get('phone');
        $data['invite_code'] = randStr();
	    $data['invite_member_id'] = Member::fetchIdWithInviteCode($data['invite_member']);
        $data['uid'] = $ret['data']['uid'];
        $data['password'] = $data['password'];

        \Log::debug($ret );
        \Log::debug($data);

        $ret = $member->create($data);
        $account_ret = Account::create(['member_id'=>$ret->id, 'is_lock'=>0]);
        Auth::guard('front')->login($ret);
        $code = session('verify_code');
        $code['valid'] = 0;
        session(['verify_code'=>$code]);
        // 生成account
        if($ret) {
            $account = Account::where('id', '=', $account_ret->id)->first();
            $account->trade_pwd =\password_hash($data['tradePassword'], PASSWORD_BCRYPT);
            $account->save();
        }
        return $ret ? ['code'=>200, 'data'=>'注册成功'] : ['code'=>202, 'data'=>'注册失败'];
    }

    public function registerSuccess()
    {
        return view('front.member.successmessage', ['message'=>'恭喜您,注册成功!']);
    }

    public function xieYi(){
        return view('front/trade_xieYi');
    }


}
