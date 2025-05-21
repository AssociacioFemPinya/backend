<?php

namespace App\Http\Middleware;

use Closure;

class AccesBoards
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->accesBoards()) {
            return $next($request);
        } else {
            abort(401);
        }
    }
}
