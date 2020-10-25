<?php

namespace App\Http\Middleware;

use App\Model\Member;
use App\Utils\ApiResUtil;
use App\Utils\ResUtil;
use Closure;

class ApiAuth
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
        $member = Member::apiCurrent();
	    if(empty($member)){
		    echo json_encode(ApiResUtil::error('请前往登录', -1));
		    exit;
	    }
	    if ($member->is_lock == 1){
            echo json_encode(ApiResUtil::error('该用户已被封禁', -1));
            exit;
        }

        return $next($request);
    }
}
