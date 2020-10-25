<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/6/9
 * Time: 下午3:41
 */

namespace App\Utils;


use App\Model\Member;
use App\Service\SsoService;

class UrlUtil
{
	public static function loginUrl()
	{
		return route('login');
	}

	public static function o2oHomeUrl()
	{
		return env('O2O_HOST', 'https://o2o.yigongpan.com');
	}

	public static function ssoLoginUrl()
	{
		return SsoService::fetchHost() . '/index.html?appid=5a88e8dedc1ecf7fb04a074bdea376cf&returnurl=' . route('login');
	}

	public static function ssoMLoginUrl()
	{
		return SsoService::fetchHost() . '/mobile/index.html?appid=5a88e8dedc1ecf7fb04a074bdea376cf&returnurl=' . route('login');
	}

	public static function isWeChatBrowser($request)
	{
		return strpos($request->header('user_agent'), 'MicroMessenger') !== false;
	}

	public static function flexLoginUrl()
	{
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
			return self::ssoMLoginUrl();
		}
		return self::ssoLoginUrl();
	}

	public static function flextHuiUrl()
	{
		if(!\Auth::guard('front')->check()) {

			if (static::check_user_agent('mobile')) {
				return self::fetchMallHost() . '/mobile/index.html';
			}
			return self::fetchMallHost() . '/index.html';
		}
		$member = Member::current();
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
			return self::fetchMallHost() . '/mobile/index.html?ticket=' . $_COOKIE['ticket'] . '&phone=' . $member->phone;
		}

		if (static::check_user_agent('mobile')) {
			return self::fetchMallHost() . '/mobile/index.html?ticket=' . $_COOKIE['ticket']  . '&phone=' . $member->phone;
		}

		return self::fetchMallHost() . '/index.html?ticket=' . $_COOKIE['ticket'] . '&phone=' . $member->phone;
	}

	public static function check_user_agent ( $type = NULL ) {
		$user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
		if ( $type == 'bot' ) {
			// matches popular bots
			if ( preg_match ( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent ) ) {
				return true;
				// watchmouse|pingdom\.com are "uptime services"
			}
		} else if ( $type == 'browser' ) {
			// matches core browser types
			if ( preg_match ( "/mozilla\/|opera\//", $user_agent ) ) {
				return true;
			}
		} else if ( $type == 'mobile' ) {
			// matches popular mobile devices that have small screens and/or touch inputs
			// mobile devices have regional trends; some of these will have varying popularity in Europe, Asia, and America
			// detailed demographics are unknown, and South America, the Pacific Islands, and Africa trends might not be represented, here
			if ( preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent ) ) {
				// these are the most common
				return true;
			} else if ( preg_match ( "/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent ) ) {
				// these are less common, and might not be worth checking
				return true;
			}
		}
		return false;
	}

	public static function fetchMallHost()
	{
		return env('YBH_HOST', 'https://mall.yigongpan.com');
	}


}