<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class TokenAuthenticationEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authConfig = Auth::User();
        $castellerConfig = $authConfig->casteller->castellerConfig;
        if (! $castellerConfig->getAuthTokenEnabled()) {
            abort(401);
        }

        $castellerConfig->last_access_at = Carbon::now()->toDateTime();
        $castellerConfig->save();

        return $next($request);
    }
}
