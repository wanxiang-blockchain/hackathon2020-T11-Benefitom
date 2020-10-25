<?php

namespace App\Http\Middleware;

use App\Model\Member;
use App\Utils\ApiResUtil;
use Closure;

class TestWhite
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
        // 内测白名单
//        $member = Member::apiCurrent();
//        if (env('APP_ENV', 'prod') !== 'prod') {
//            return $next($request);
//        }
//        if ($member && !in_array($member->phone, ['15001204748', '18611010126', '13659828348'])){
//            echo json_encode(ApiResUtil::error('系统内测中，请稍候访问'));
//            exit;
//        }
        return $next($request);
    }
}
