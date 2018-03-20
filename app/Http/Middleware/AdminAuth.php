<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Disable devbug bar
        \Debugbar::disable();

        // Check if auuthenticated
        if( \Auth::check() )
            return $next($request);
        else
            return redirect()->action('Admin\AccountController@login', [ 'request_uri' => $request->fullUrl() ]);
    }
}
