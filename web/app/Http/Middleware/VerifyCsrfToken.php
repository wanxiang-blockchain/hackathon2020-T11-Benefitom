<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'pay/notify',
        'wechatNotify',
        'back_notify',
        'back',
	    'score/consume',
	    'score',
	    'tender/paycallback',
	    'trade/tradeOrder',
	    'wx/recharge',
	    'wxpay/callback'
    ];
}
