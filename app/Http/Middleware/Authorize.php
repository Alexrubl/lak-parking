<?php

namespace App\Http\Middleware;

use Laravel\Nova\Nova;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request):mixed  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        if (\Auth::user()->isTenant()) {
            Nova::initialPath('/resources/transports');
        }

        return Nova::check($request) ? $next($request) : abort(403);
    }
}
