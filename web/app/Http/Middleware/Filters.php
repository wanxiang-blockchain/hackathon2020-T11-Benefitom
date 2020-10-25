<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;

class Filters
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
        $response =  $next($request);
//        $RememberCookieName = \Auth::guard('front')->getRecallerName();
//        $RememberCookieValue = Cookie::get($RememberCookieName);
//        $first_name = 'remember_front_first';
//        if($RememberCookieValue) {
//            $login_first = Cookie::get($first_name);
//            if(!$login_first) {
//                $remember = Cookie::make($RememberCookieName, $RememberCookieValue, 3*1440);
//                $remember_first =  Cookie::make($first_name, 1, 3*1440);
//                return $response->withCookie($remember)->withCookie($remember_first);
//            }
//        }else{
//            $cookie = Cookie::forget($first_name);
//            return $response->withCookie($cookie);
//        }
        return $response;
    }
}
