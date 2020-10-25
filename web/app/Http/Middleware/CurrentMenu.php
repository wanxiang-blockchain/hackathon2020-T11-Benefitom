<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\Module;
use App\Helpers\Menu;

class CurrentMenu
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
        $nav = $request->input("nav");
        if ($nav) {
            session()->put("nav", $nav);
        }
        //放置默认的导航信息
        if (!session()->has('nav')) {
            session()->put("nav", '2|1');
        }
        view()->share('crumbs',Menu::Crumbs());
        view()->share('menu' ,Menu::Menu());

        if (\Auth::guest()) {
            $request->session()->flush();
            $request->session()->regenerate();
            return redirect('/admin/login');
        }
        return $next($request);
    }
}
