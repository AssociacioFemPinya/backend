<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-focus">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, user-scalable=no">

    <title>{{ config('app.name', 'FemPinya') }} | @yield('title', 'Inici')</title>

    <meta name="description" content="FemPinya és un sistema de gestió de la participació en les activitats de les colles castelleres, muixerangues i grups semblants, sota la filosofia col·laborativa i de treball en equip">
    <meta name="author" content="Associació FemPinya">
    <meta name="robots" content="noindex, nofollow">

    <!-- Open Graph Meta -->
    <meta property="og:title" content="{{ config('app.name', 'FemPinya') }}">
    <meta property="og:site_name" content="{{ config('app.name', 'FemPinya') }}">
    <meta property="og:description" content="FemPinya és un sistema de gestió de la participació en les activitats de les colles castelleres, muixerangues i grups semblants, sota la filosofia col·laborativa i de treball en equip">
    <meta property="og:type" content="website">
    <meta property="og:url" content="fempinya.cat">
    <meta property="og:image" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('media/favicons/favicon.png') }}">
    <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('media/favicons/favicon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/favicons/apple-touch-icon-180x180.png') }}">
    <!-- END Icons -->

    <!-- Stylesheets -->
    @yield('css_before')
    <!-- Fonts and Codebase framework -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,400i,600,700&display=swap">
    <link rel="stylesheet" id="css-main" href="{{ mix('/css/codebase.css') }}">
    <!--link rel="stylesheet" id="css-theme" href="{{ mix('/css/themes/corporate.css') }}"-->
    @yield('css_after')
</head>
<body>
        <div id="page-container" class=" main-content-boxed " >
        @include('public.display.navbar-top')

        <!-- Main Container -->

        <main id="main-container" style="overflow: auto !important">

            <div class="bg-body-light bg-pattern" id="app">

                @include('template.partials.session-flash-notifications')

                @yield('content')

            </div>
        </main>
        <!-- END Main Container -->
        <!-- Footer -->
        <footer id="page-footer" class="opacity-0">
            <row>
                <div class="content text-center font-weight-bold font-size-h5">
                    {{ config('app.name', 'FemPinya') }} - {{ date("Y") }}
                </div>
            </row>
        </footer>
        <!-- END Footer -->
    </div>
    <!-- END Page Container -->


<script src="{{ mix('js/codebase.app.js') }}"></script>
<script src="{{ mix('js/laravel.app.js') }}"></script>

@yield('js')

</body>
</html>
