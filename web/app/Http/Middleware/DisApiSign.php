<?php

namespace App\Http\Middleware;

use App\Utils\ApiResUtil;
use App\Utils\DissysPush;
use Closure;
use Illuminate\Support\Facades\Log;

class DisApiSign
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

        if (env('APP_ENV', 'prod') === 'local') {
            return $next($request);
        }
    	//    sign: sha1(keysort(params) + appkey + timestamp) 按 key 正序。比如 ['nationcode' => '86', 'phone' => '110', 'id' => 1]; 排序拼起来就是 11108
        //    appkey: test(Llasd1jksafasdl) prod(Pdlak282200J323)
        $appkey = DissysPush::appkey();
        $sign = $request->get('sign');
        $timestamp = $request->get('timestamp');
        Log::debug(__CLASS__ , [
            'data' => $request->all(),
            'sign' => $sign,
            'timestamp' => $timestamp,
            'headers' => $request->headers
        ]);
        if (empty($sign) || empty($timestamp)) {
            echo json_encode(ApiResUtil::error('invalid sign'));
            exit;
        }
        if ($timestamp < time() - 7200) {
            echo json_encode(ApiResUtil::error('expired sign'));
            exit;
        }
        $data = $request->all();
    	unset($data['timestamp'], $data['sign']);
    	ksort($data);
    	$signStr = implode('', $data) . $appkey . $timestamp;
    	$sign1 = sha1($signStr);
        Log::debug('signStr', [
            'signStr' => $signStr,
            'sign' => $sign,
            'sign1' => $sign1
        ]);
    	if ($sign !== $sign1) {
            echo json_encode(ApiResUtil::error('invalid sign'));
    		exit;
	    }
        return $next($request);
    }
}
