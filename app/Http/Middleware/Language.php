<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Language
{
    public function handle(Request $request, Closure $next)
    {

        if (! empty(Auth::user()->language)) {
            App::setLocale(Auth::user()->language);
            setlocale(LC_MONETARY, 'es_ES');
        }

        return $next($request);
    }
}
