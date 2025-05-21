<?php

if (isset($_GET['lang']))
{
    $languages = [$_GET['lang']];
}
else
{
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
    {
        $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    }
    else
    {
        $languages = ['es']; //default ES
    }
}



if ((in_array("ca", $languages)) || (in_array("ca-es", $languages)) || (in_array("ca-ES", $languages)) || (in_array("ca_ES", $languages)))
{
    App::setLocale("ca");
}
elseif ((in_array("es", $languages)) || (in_array("es-ES", $languages)) || (in_array("es_ES", $languages)))
{
    App::setLocale("es");
}
else
{
    App::setLocale("en");
}
?>
@extends('template.app')

@section('content')
<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-lg-8">
            <p class="text-center">
                <img src="{{ asset('media/img/logo-vertical.png') }}" alt="FemPinya">
                <!--span style="margin-right: 15px"></span><img src="{{ asset('media/img/logo.png') }}">
                <span style="margin-left: -15px;"></span><img style="margin-bottom: -25px;" src="{{ asset('media/img/logo-text.png') }}"-->
            </p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-body">

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ trans("auth.e-mail") }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ trans("auth.password")  }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ trans('auth.remember_me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ trans('auth.login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ trans('auth.password_lost') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
