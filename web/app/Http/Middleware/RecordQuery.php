<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RecordQuery
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
    	if ($request->isMethod('post')) {
#		    \DB::statement("set tx_isolation='SERIALIZABLE';");
    		Log::info('request: ', [
    			'url' => $request->url(),
    			'params:' => $request->all()
			]);
	    }
        $res = $next($request);
	    if ($request->isMethod('post')) {
		    Log::info($res);
	    }
	    return $res;
    }
}
