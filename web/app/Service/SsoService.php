<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/6/11
 * Time: 上午10:09
 */

namespace App\Service;


use App\Exceptions\SsoException;
use App\Exceptions\TradeException;
use App\Model\Artbc\Msgcode;
use App\Model\Member;
use App\Utils\ApiResUtil;
use App\Utils\DateUtil;
use App\Utils\HttpUtil;
use App\Utils\QcloudSms;
use App\Utils\SmsUtil;
use Illuminate\Support\Facades\Log;

class SsoService
{
	const APPID = '5a88e8dedc1ecf7fb04a074bdea376cf';

	const HOST = 'https://passport.yigongpan.com';

	const API_HOST = 'https://data.yigongpan.com';

	public static $ticket;
	public static $uid;

	public static function fetchApiHost()
	{
		return env('SSO_API_HOST', self::API_HOST);
	}

	public static function fetchHost()
	{
		return env('SSO_HOST', self::HOST);
	}

	/**
	 * 向sso验证tk
	 * @desc verify
	 * @param $tk
	 * @return bool
	 */
	public static function verify($tk)
	{
		if ($m = Member::fetchByTk($tk)){
		    SsoService::$ticket = $tk;
			return [
				'uid' => $m->uid,
				'ticket' => $m->tk,
				'expires' => time() + 365 * 86400
			];
		}
		return null;
//		$url = self::fetchApiHost() . '/user/vertify';
//		$data = [
//			'appid' => self::APPID,
//			'ticket' => $tk
//		];
//		$res = self::post($url, $data);
//		if ($res === false || $res['code'] !=0 ) {
//			return false;
//		}
//		return $res['data'];
	}

	/**
	 * @desc profile
	 * @param $uid
	 * @return bool|mixed
	 */
	public static function profile($uid)
	{
		$url = self::fetchApiHost() . '/user/profile';
		$data = [
			'appid' => self::APPID,
			'uid' => $uid
		];
		$res = self::get($url, $data);
		if ($res === false || $res['code'] !=0 ) {
			return false;
		}
		return $res['data'];
	}

	/**
	 * 发短信验证码
	 * @desc sms
	 * @param $phone
	 * @return array
	 */
    public static function sms($phone, $nationcode = '86', $type=0)
	{
	    $code = rand(100000, 999999);
	    $nationcode = trim($nationcode, '+');
	    $cond = [
	        'phone' => $phone,
            'nationcode' => $nationcode,
            'type' => $type
        ];
	    // 限制频次
        try{
            $msgcode = Msgcode::where($cond)->orderByDesc('id')->first();
            if ($msgcode && strtotime($msgcode->created_at) + 300 > time()){
                return static::error('请求短信太过频繁');
            }
            if (Msgcode::where($cond)->where('created_at', '>', DateUtil::today())->count('id') > 10) {
                return static::error('该手机号已到短信发送限制');
            }
            if (!QcloudSms::send($nationcode, $phone, $code)) {
                throw new TradeException('短信发送失败：code3');
            }
            Msgcode::add($phone, $nationcode, $code, $type);
            return static::ok();
        }catch (\Exception $e) {
            \Log::error($e->getMessage());
            return self::error($e->getMessage());
        }

	}

	public static function smsVerify($phone, $nationcode, $code, $type=0)
	{
		if (env('APP_ENV', 'prod') == 'local') {
			return ['code' => 0];
		}
        if (env('APP_ENV', 'prod') === 'test' && $code === '2288') {
            return ['code' => 0];
        }
		if ($phone == '15001204748' && env('APP_ENV', 'prod') !== 'prod' && $code === '1829') {
		    return self::ok();
        }
        $model = Msgcode::fetchModelWithCode($phone, $nationcode, $code, $type);
		if (!$model) {
		    return static::error('验证码不正确');
        }
		if ($model->expires <= time()) {
		    \Log::debug($model->toArray());
		    return static::error('验证码已过期，请重新请求');
        }
		$model->stat = Msgcode::STAT_USED;
		$model->save();
		return self::ok();

	}

	public static function reg($phone, $pwd, $code)
	{
        return [
            'code' => 0,
            'data' => [
                'uid' => uniqid(),
            ]
        ];
		$url = self::fetchApiHost() . '/user/register';
		$data = [
			'phone' => $phone,
			'pwd1' => $pwd,
			'pwd2' => $pwd,
			'appid' => self::APPID,
			'code' => $code
		];
		return self::post($url, $data);
	}

	public static function login($phone, $pwd)
	{
        $member = Member::where([
                'phone'    => $phone
            ])->first();
        if ($pwd == $member->password){
            $member->remember_token = uniqid() . rand(10000, 99999);
			self::$ticket = $member->remember_token;
            $member->save();
			self::$uid = $member->uid;
            return true;
        }else{
            return false;
        }
		$url = self::fetchApiHost() . '/user/login';
		$data = [
			'phone' => $phone,
			'pwd' => $pwd,
			'appid' => self::APPID
		];
		$ret = self::post($url, $data);
		if ($ret['code'] == 0) {
			self::$ticket = $ret['data']['ticket'];
			self::$uid = $ret['data']['uid'];
			return true;
		}
		return false;
	}

	public static function resetPwd($phone, $pwd, $code)
	{
		$url = self::fetchApiHost() . '/user/reset';
		$data = [
			'phone' => $phone,
			'pwd1' => $pwd,
			'pwd2' => $pwd,
			'appid' => self::APPID,
			'code' => $code
		];
		return self::post($url, $data);

	}

	public static function post($url, $body)
	{
		$res = HttpUtil::post($url, $body);
		$res = json_decode($res, true);
		if(!isset($res['errCode'])) {
			throw new SsoException('调用sso异常');
		}
		return [
			'code' => $res['errCode'],
			'data' => $res['content']
		];
	}

	public static function get($url, $body)
	{
		$res = HttpUtil::get($url, $body);
		$res = \GuzzleHttp\json_decode($res, true);
		if(!isset($res['errCode'])) {
			throw new SsoException('调用sso异常');
		}
		return [
			'code' => $res['errCode'],
			'data' => $res['content']
		];
	}

	public static function setCookie($key, $value, $expires=0)
	{
		$secure = true;
		if (env('APP_DEBUG', false)) {
			$secure = false;
		}
		return setcookie($key, $value, $expires, '/', '', $secure, true);
	}

	public static function error($data)
    {
        return ['code' => 201, 'data' => $data];
    }

    public static function ok()
    {
        return ['code' => 0];
    }

}
