<?php

namespace App\Http\Controllers\Admin;

use App\Model\Member;
use App\Service\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Service\ValidatorService;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private $root = "/admin";

    public function __construct()
    {
    }

    public function postLogin(Request $request, ValidatorService $validatorService, SmsService $service) {
        Log::debug(request()->all());
        $data = $request->all();
        $password = $request->get('password');
        $decrypted = "";
        if (openssl_private_decrypt(base64_decode($password),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $data['password'] = $decrypted;
        } else {
            die();
        }

        $validator = $validatorService->checkValidator([
            'phone'    => 'required',
            'password' => 'required'
        ], $data);
        if($validator['code'] != 200) {
            return $validator;
        }
//        if(!in_array($data['phone'], ['13659828348', '18611010126', '13901184287'])){
	        $validator = $service->verifyCode($data['verificationCode'], $data['phone']);
	        if($validator['code'] != 200) {
		        return $validator;
	        }
//        }
        if (Auth::guard('web')->attempt(['phone' => $request->get("phone"), 'password' => $data['password']])) {
            $code = session('verify_code');
            $code['valid'] = 0;
            session(['verify_code'=>$code]);
            return ['code'=>200,'data'=>'成功'];
        } else {

	        return ['code'=>201,'data'=>'账号或密码不匹配'];
        }
    }
    public function getLogin() {
        if (\Auth::guard('web')->user()) {
            return redirect("{$this->root}/hello?nav=1");
        }
        $key = trim(file_get_contents("../rsa_1024_pub.pem"));
        return view('admin.login',compact('key'));
    }

    public function logout() {
        $request = request();
        $front = \Auth::guard('front')->user();
        \Auth::guard('web')->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        if(!is_null($front)) {
            \Auth::guard('front')->login($front);
        }
        return redirect("{$this->root}");
    }
}
