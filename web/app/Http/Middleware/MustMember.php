<?php

namespace App\Http\Middleware;

use App\Exceptions\SsoException;
use App\Model\Member;
use App\Service\SsoService;
use App\Utils\UrlUtil;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Model\Account;

class MustMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'front')
    {
    	\Log::debug(__CLASS__, [
             'guard' => $guard,
             'check' => Auth::guard($guard)->check()
        ]);
    	if (!Auth::guard($guard)->check()) {
    	    $query = $request->query();
    	    $query['prev_action'] = urlencode(url()->current());
    	    $querystr = http_build_query($query);
    	    \Log::debug(__CLASS__ . 'query', $query);
    		return redirect(route('login') . '?' . $querystr);
	    }
	    $user = Auth::guard($guard)->user();
    	$id = $user->id;
    	$member = Member::where(['id'=>$id])->first();
	    if($member->is_lock == 1) {
	        Auth::guard($guard)->logout();
	        return redirect('/login');
        }

        return $next($request);
    }
}
