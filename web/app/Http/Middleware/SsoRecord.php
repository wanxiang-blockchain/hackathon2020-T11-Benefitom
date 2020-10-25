<?php

namespace App\Http\Middleware;

use App\Model\Account;
use App\Model\Member;
use App\Service\SsoService;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Mockery\Exception;

class SsoRecord
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
	    // http://localhost:9998/
	    // 1、取ticket uid调sso验证
	    // 2、存ticket uid于缓存
	    // 3、种cookie
	    // 4、如果url中无ticket，从cookie中取，如果无跳转sso页

	    $ticket = $request->get('ticket');

	    empty($ticket) && $ticket = isset($_COOKIE['ticket']) ? $_COOKIE['ticket'] : null;

	    // 在所有页面，如果无ticket或者是假ticket，不过该中间件
	    if (empty($ticket) || ($data = SsoService::verify($ticket)) === false) {
	    	return $next($request);
	    }


	    SsoService::setCookie('ticket', $ticket, $data['expires']);

        return $next($request);
    }
}
