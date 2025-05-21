<?php

namespace App\Http\Middleware;

use Closure;

class AccesNotifications
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->accesNotifications()) {
            return $next($request);
        } else {
            abort(401);
        }
    }
}
