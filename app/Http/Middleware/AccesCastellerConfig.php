<?php

namespace App\Http\Middleware;

use Closure;

class AccesCastellerConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->accesCastellerConfig()) {
            return $next($request);
        } else {
            abort(401);
        }
    }
}
