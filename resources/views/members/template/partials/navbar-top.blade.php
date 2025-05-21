            <!-- Header -->
            <header id="page-header">
                <!-- Header Content -->
                <div class="content-header">
                    <!-- Left Section -->
                    <div class="content-header-section">
                        <!-- Logo -->
                        <a class=" font-w700" href="">
                            <img class="pb-5" src="{!! asset('media/img/logo.svg') !!}" alt="FemPinya" width="40">
                            @if($colla->getLogo())
                                 <img src="{!! asset('media/colles/'.$colla->getShortName().'/'.$colla->getLogo()) !!}" class="img-pb-5" alt="Logo: {!! $colla->getName() !!}"  width="40">
                            @endif
                        </a>
                        <!-- END Logo -->
                    </div>
                    <!-- END Left Section -->

                    <!-- Middle Section -->
                    <div class="content-header-section d-none d-lg-block">
                        <!-- Header Navigation -->
                        <!--
                        Desktop Navigation, mobile navigation can be found in #sidebar

                        If you would like to use the same navigation in both mobiles and desktops, you can use exactly the same markup inside sidebar and header navigation ul lists
                        If your sidebar menu includes headings, they won't be visible in your header navigation by default
                        If your sidebar menu includes icons and you would like to hide them, you can add the class 'nav-main-header-no-icons'
                        -->
                        <ul class="nav-main-header">
                            <li class="nav-main-heading">
                                Heading
                            </li>
                            <li>
                                <a class="nav-submenu" data-toggle="nav-submenu" href="#">
                                    <i class="si si-calendar"></i> {!! trans('general.calendar') !!}
                                </a>
                                <ul>
                                    <li>
                                        <a href="{{ route('member.calendar') }}?event_type={!! \App\Enums\EventTypeEnum::ASSAIG!!}">{!! $colla->config->getTranslationAssaig()?: __('config.translation_assaig')   !!}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('member.calendar') }}?event_type={!! \App\Enums\EventTypeEnum::ACTUACIO!!}">{!! $colla->config->getTranslationActuacio()?: __('config.translation_actuacio')  !!}</a>
                                    </li>
                                    <li>

                                        <a href="{{ route('member.calendar') }}?event_type={!! \App\Enums\EventTypeEnum::ACTIVITAT!!}">{!! $colla->config->getTranslationActivitat()?: __('config.translation_activitat') !!}</a>
                                    </li>
                                </ul>
                            </li>
                            @if(Auth::user()->casteller->getColla()->getConfig()->getBoardsEnabled())
                            <li>
                                <a class="nav-submenu" data-toggle="nav-submenu" href="#">
                                    <i class="fa fa-map-o" aria-hidden="true"></i> {!! trans('general.pinyes') !!}
                                </a>
                                <ul>
                                    <li>
                                        <a href="{{ route('member.pinyes') }}">{!! trans('botman.pinya') !!}</a>
                                    </li>

                                    <li>
                                        <a href="{{ route('member.rondes') }}">{!! trans('botman.rondes') !!}</a>
                                    </li>
                                </ul>
                            </li>
                            @endif
                            <li>
                                <a href="{{ route('member.notifications.list') }}">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> {!! trans('general.notifications') !!}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('member.verify.token.form') }}">
                                    <i class="fa-solid fa-check-double" aria-hidden="true"></i> {!! trans('tokentotp.verify_attendance') !!}
                                </a>
                            </li>
                        </ul>
                        <!-- END Header Navigation -->
                    </div>
                    <!-- END Middle Section -->

                    <!-- Right Section -->
                    <div class="content-header-section">
                        <!-- Color Themes + A few of the many header options (used just for demonstration) -->
                        <!-- Themes functionality initialized in Codebase() -> uiHandleTheme() -->
                        <div class="btn-group ml-5" role="group">
                            <div class="dropdown-menu dropdown-menu-right min-width-150" aria-labelledby="page-header-themes-dropdown">
                                <h6 class="dropdown-header text-center">Color Themes</h6>
                                <div class="row no-gutters text-center">
                                    <div class="col-4 mb-5">
                                        <a class="text-default" data-toggle="theme" data-theme="default" href="javascript:void(0)">
                                            <i class="fa fa-2x fa-circle"></i>
                                        </a>
                                    </div>
                                    <div class="col-4 mb-5">
                                        <a class="text-elegance" data-toggle="theme" data-theme="assets/css/themes/elegance.min.css" href="javascript:void(0)">
                                            <i class="fa fa-2x fa-circle"></i>
                                        </a>
                                    </div>
                                    <div class="col-4 mb-5">
                                        <a class="text-pulse" data-toggle="theme" data-theme="assets/css/themes/pulse.min.css" href="javascript:void(0)">
                                            <i class="fa fa-2x fa-circle"></i>
                                        </a>
                                    </div>
                                    <div class="col-4 mb-5">
                                        <a class="text-flat" data-toggle="theme" data-theme="assets/css/themes/flat.min.css" href="javascript:void(0)">
                                            <i class="fa fa-2x fa-circle"></i>
                                        </a>
                                    </div>
                                    <div class="col-4 mb-5">
                                        <a class="text-corporate" data-toggle="theme" data-theme="assets/css/themes/corporate.min.css" href="javascript:void(0)">
                                            <i class="fa fa-2x fa-circle"></i>
                                        </a>
                                    </div>
                                    <div class="col-4 mb-5">
                                        <a class="text-earth" data-toggle="theme" data-theme="assets/css/themes/earth.min.css" href="javascript:void(0)">
                                            <i class="fa fa-2x fa-circle"></i>
                                        </a>
                                    </div>
                                </div>
                                <h6 class="dropdown-header text-center">Header</h6>
                                <button type="button" class="btn btn-sm btn-block btn-alt-secondary" data-toggle="layout" data-action="header_fixed_toggle">Fixed Mode</button>
                                <button type="button" class="btn btn-sm btn-block btn-alt-secondary mb-10" data-toggle="layout" data-action="header_style_inverse_toggle">Style</button>
                            </div>
                        </div>
                        <!-- END Color Themes + A few of the many header options -->

                        <!-- Open Search Section -->
                        <!-- Layout API, functionality initialized in Codebase() -> uiApiLayout() -->

                        <!-- END Open Search Section -->

                        <!-- Toggle Sidebar -->
                        <!-- Layout API, functionality initialized in Codebase() -> uiApiLayout() -->
                        <button type="button" class="btn btn-circle btn-dual-secondary d-lg-none" data-toggle="layout" data-action="sidebar_toggle">
                            <i class="fa fa-navicon"></i>
                        </button>
                        <!-- END Toggle Sidebar -->
                    </div>

                    <!-- User Dropdown -->
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-rounded btn-dual-secondary" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           @if( Auth::user()->casteller->getAlias() )
                                <img src=" {!! Auth::user()->casteller->getProfileImage() !!}" class="img-avatar img-avatar32" alt="">
                                <i class="fa fa-angle-down ml-5"></i>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-right min-width-150" aria-labelledby="page-header-user-dropdown">

                            @if( Auth::user()->casteller->getAlias() )
                            <a class="dropdown-item" href="{{ route('member.profile') }}">
                                <i class="si si-user mr-5"></i> {!! Auth::user()->casteller->getAlias() !!}
                            </a>
                            @endif

                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">

                                <i class="si si-logout mr-5"></i> {!! trans('general.logout') !!}
                            </a>
                            <form id="logout-form" action="{{ route('member.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                    <!-- END User Dropdown -->
                    <!-- END Right Section -->
                </div>
                <!-- END Header Content -->

                <!-- Header Search -->
                <div id="page-header-search" class="overlay-header">
                    <div class="content-header content-header-fullrow">
                        <form>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <!-- Close Search Section -->
                                    <!-- Layout API, functionality initialized in Codebase() -> uiApiLayout() -->
                                    <button type="button" class="btn btn-secondary px-15" data-toggle="layout" data-action="header_search_off">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <!-- END Close Search Section -->
                                </div>
                                <input type="text" class="form-control" placeholder="Search or hit ESC.." id="page-header-search-input" name="page-header-search-input">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-secondary px-15">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- END Header Search -->

                <!-- Header Loader -->
                <div id="page-header-loader" class="overlay-header bg-primary">
                    <div class="content-header content-header-fullrow text-center">
                        <div class="content-header-item">
                            <i class="fa fa-sun-o fa-spin text-white"></i>
                        </div>
                    </div>
                </div>
                <!-- END Header Loader -->
            </header>
            <!-- END Header -->
