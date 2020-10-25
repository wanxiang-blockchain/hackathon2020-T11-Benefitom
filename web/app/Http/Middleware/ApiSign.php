<?php

namespace App\Http\Middleware;

use App\Utils\ApiResUtil;
use App\Utils\ResUtil;
use Closure;

class ApiSign
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
    	if (env('APP_DEBUG')) {
    		return $next($request);
	    }
    	$appids = [
    	    'score_asdfksjaosdi' => 'Kadfnoi1)af!_*kdafd',
    	    'artbc_appid' => ':kdaOOINfaoiwe10(Jk123r'
	    ];

    	$appid = $request->input('appid', '');

//    	if(!isset($appids[$appid])){
//    		echo json_encode(ResUtil::error(202, 'invalid appid'));
//    		exit;
//	    }

    	$sign = $request->input('sign');
    	unset($_POST['sign']);
    	ksort($_POST);
    	$sign1 = md5(implode('|', $_POST). $appids[$appid]);
    	if (empty($sign) || $sign != $sign1) {
    		echo json_encode(ApiResUtil::error('invalid sign'));
    		exit;
	    }
        return $next($request);
    }
}
