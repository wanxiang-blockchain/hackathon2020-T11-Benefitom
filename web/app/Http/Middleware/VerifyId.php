<?php

namespace App\Http\Middleware;

use App\Model\Member;
use Closure;

class VerifyId
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
    	$member = Member::current();
    	if ($member){
    		if (empty($member->name)){
    			return redirect('member/userinfoEdit');
		    }
	    }
        return $next($request);
    }
}
