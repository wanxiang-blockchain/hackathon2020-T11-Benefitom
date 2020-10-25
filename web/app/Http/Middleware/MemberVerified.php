<?php

namespace App\Http\Middleware;

use App\Model\Member;
use App\Model\Profile;
use App\Utils\ApiResUtil;
use Closure;

class MemberVerified
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
//        $member = Member::apiCurrent();
//        if (empty($member) || !Profile::isMemberVerified($member->id)){
//            echo json_encode(ApiResUtil::error('请先前往实名', -2));
//            exit;
//        }
        return $next($request);
    }
}
