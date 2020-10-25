<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-07
 * Time: 13:33
 */

namespace App\Http\Middleware;

use App\Utils\DisVerify;
use App\Utils\ApiResUtil;
use Closure;

class DisApiVerify
{
    public function handle($request, Closure $next)
    {
        if (!DisVerify::verifyTk($request->get('ticket'))){
            echo json_encode(ApiResUtil::error('身份验证失败'));
            exit;
        }

        return $next($request);
    }
}