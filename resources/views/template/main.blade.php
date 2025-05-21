<!doctype html>
<html lang="ca" class="no-focus">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>{{ config('app.name', 'FemPinya') }} | @yield('title', 'Inici')</title>

    <meta name="description" content="FemPinya és un sistema de gestió de la participació en les activitats de les colles castelleres, muixerangues i grups semblants, sota la filosofia col·laborativa i de treball en equip">
    <meta name="author" content="{{ config('app.name', 'FemPinya') }}">
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

    <!-- Fonts and Styles -->
    @yield('css_before')
    @stack('css_before')
    {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,400i,600,700"> --}}
    {{-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> --}}
    {{--  <link href="https://fonts.googleapis.com/css?family=Bitter:300,400,400i,600,700"> --}}

    <link rel="stylesheet" id="css-main" href="{{ mix('/css/codebase.css') }}">

    <!-- You can include a specific file from public/css/themes/ folder to alter the default color theme of the template. eg: -->
    <!--link rel="stylesheet" id="css-theme" href="{{ mix('/css/themes/corporate.css') }}"-->
    @yield('css_after')

    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/flat.min.css"> -->
    <!-- END Stylesheets -->

</head>
<body>
    <div id="page-container" class="sidebar-o sidebar-inverse enable-page-overlay side-scroll main-content-boxed " >

        @include('template.partials.sidebar-left')

        @include('template.partials.navbar-top')

        <!-- Main Container -->
        <main id="main-container" >

            <div class="m-20" id="app">
                @include('template.partials.session-flash-notifications')

                <!-- Page Content -->
                @yield('content')
                <!-- END Page Content -->
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

    <!-- Codebase Core JS -->
    <script src="{{ mix('js/codebase.app.js') }}"></script>

    <!-- Laravel Scaffolding JS -->
    <script src="{{ mix('js/laravel.app.js') }}"></script>


    {{-- Web Widget code --}}
    {{-- Sample code about how to costumize the web widget --}}
    {{-- TODO: get casteller ID instead of the USER ID to pass into the botman --}}
    <?php /*
    <script>
        var botmanWidget = {
        title: '{{ __('botman.driver_web_widget_title') }}',
        placeholderText: '{{   __('botman.driver_web_widget_placeholder') }}',
        userId: {{ auth()->user()->getAuthIdentifier() }},
        };
    </script>
    <script id="botmanWidget" src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/chat.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>
    */
    ?>

    @yield('js')
</body>
</html>
