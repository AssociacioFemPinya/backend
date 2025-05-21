<?php

namespace App\Http\Controllers\Auth;

use App\AuthConfig;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TokenAuthController extends Controller
{
    /** login as authenticableMember with token */
    public function login(): RedirectResponse
    {
        $authConfig = AuthConfig::find(Auth::User()->id_auth_config);
        $sessionExpires = $authConfig->getColla()->config->getMemberSessionExpire();
        Auth::guard('member')->login($authConfig, ! $sessionExpires);

        return redirect()->route('member.calendar');
    }
}
