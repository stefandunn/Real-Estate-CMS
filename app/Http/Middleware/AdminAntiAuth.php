<?php

namespace App\Http\Middleware;

use Closure;

class AdminAntiAuth
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
        // Disable devbug bar
        \Debugbar::disable();

        // Check if auuthenticated
        if( \Auth::check() )
            return redirect()->action('Admin\DashboardController@index');
        else
            return $next($request);
    }
}
