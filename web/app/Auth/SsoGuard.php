<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/6/11
 * Time: 下午9:39
 */

namespace App\Auth;


use App\Model\Member;
use App\Service\SsoService;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Authenticatable;

class SsoGuard implements Guard
{
	use GuardHelpers;

	/**
	 * Get the currently authenticated user.
	 * 精华在此，在此间获取 Member 实体作为 user 值
	 *
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function user()
	{
		if (!is_null($this->user)) {
			return $this->user;
		}

		// 从session 中取 ticket
		// 用uid从member中取Model
        $tk = isset($_COOKIE['ticket']) ? $_COOKIE['ticket'] : (isset(SsoService::$ticket) ? SsoService::$ticket : null);


		if (empty($tk)) {
			return null;
		}

		$user = Member::fetchByTk($tk);
		$this->user = $user;
		return $this->user;
	}


	/**
	 * Validate a user's credentials.
	 *
	 * @param  array  $credentials
	 * @return bool
	 */
	public function validate(array $credentials = [])
	{

	}


	/**
	 * Log the user out of the application.
	 *
	 * @return void
	 */
	public function logout()
	{

		$this->user = null;

		$this->loggedOut = true;
		SsoService::setCookie('ticket', '', -1);
	}

	/**
	 * 记录登录信息
	 * @desc login
	 */
	public function login()
	{
//		SsoService::setCookie('ticket', SsoService::$ticket, time() + 86400);
//		SsoService::setCookie('uid', SsoService::$uid, time() + 86400);
	}


}