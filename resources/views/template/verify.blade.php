<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-focus">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, user-scalable=no">

    <title>{{ config('app.name', 'FemPinya') }} | @yield('title', 'Verificación')</title>

    <meta name="description" content="FemPinya - Verificación de asistencia">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('media/favicons/favicon.png') }}">
    <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('media/favicons/favicon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/favicons/apple-touch-icon-180x180.png') }}">

    <!-- Stylesheets -->
    @yield('css_before')
    <link rel="stylesheet" id="css-main" href="{{ mix('/css/codebase.css') }}">
    @yield('css_after')
</head>
<body>
    <div id="page-container" class="main-content-boxed">
        <!-- Main Container -->
        <main id="main-container" style="padding-top: 0;">
            <div class="bg-pattern" id="app">
                @yield('content')
            </div>
        </main>
        <!-- END Main Container -->

        <!-- Footer -->
        <footer id="page-footer" class="opacity-0">
            <div class="content text-center font-weight-bold font-size-h5">
                {{ config('app.name', 'FemPinya') }} - {{ date("Y") }}
            </div>
        </footer>
        <!-- END Footer -->
    </div>
    <!-- END Page Container -->

    <script src="{{ mix('js/codebase.app.js') }}"></script>
    <script src="{{ mix('js/laravel.app.js') }}"></script>

    @yield('js')
</body>
</html>
