<?php

namespace App\Http;

use App\Http\Middleware\ApiAuth;
use App\Http\Middleware\ApiSign;
use App\Http\Middleware\Appid;
use App\Http\Middleware\Broadcast;
use App\Http\Middleware\DisApiSign;
use App\Http\Middleware\DisApiVerify;
use App\Http\Middleware\MemberVerified;
use App\Http\Middleware\OpenApiSign;
use App\Http\Middleware\RecordQuery;
use App\Http\Middleware\SsoRecord;
use App\Http\Middleware\TestWhite;
use App\Http\Middleware\VerifyId;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
             \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
           // \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
	        RecordQuery::class,
        ],

        'api' => [
            'throttle:15,1',
            'bindings',
//            'appid'
            'test.white'
        ],

	    'sso' => [
	    	SsoRecord::class,
	    ],
	    'sign' => [
	        ApiSign::class,
	    ],
	    'apiauth' => [
	    	ApiAuth::class
	    ]
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'admin' => \App\Http\Middleware\MustAdmin::class,
        'member' => \App\Http\Middleware\MustMember::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'curmenu'  =>  \App\Http\Middleware\CurrentMenu::class,
        'wechat.oauth' => \Overtrue\LaravelWechat\Middleware\OAuthAuthenticate::class,
        'filters' => \App\Http\Middleware\Filters::class,
	    'broadcast' => Broadcast::class,
	    'verifyid' => VerifyId::class,
	    'auth.api' => ApiAuth::class,
	    'record.query' => RecordQuery::class,
        'disapi' => DisApiSign::class,
        'appid' => Appid::class,
        'disverify' => DisApiVerify::class,
        'member.verified' => MemberVerified::class,
        'openapi' => OpenApiSign::class,
        'test.white' => TestWhite::class
    ];
}
