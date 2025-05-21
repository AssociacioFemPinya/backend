            <!-- Sidebar -->
            <nav id="sidebar">
                <!-- Sidebar Content -->
                <div class="sidebar-content">
                    <!-- Side Header -->
                    <div class="content-header content-header-fullrow bg-black-op-10">
                        <div class="content-header-section text-center align-parent">
                            <!-- Close Sidebar, Visible only on mobile screens -->
                            <!-- Layout API, functionality initialized in Codebase() -> uiApiLayout() -->
                            <button type="button" class="btn btn-circle btn-dual-secondary d-lg-none align-v-r" data-toggle="layout" data-action="sidebar_close">
                                <i class="fa fa-times text-danger"></i>
                            </button>
                            <!-- END Close Sidebar -->

                            <!-- Logo -->
                            <div class="content-header-item">
                            <img class="pb-5" src="{!! asset('media/img/logo.svg') !!}" alt="FemPinya" width="40"> FemPinya
                            </div>
                            <!-- END Logo -->
                        </div>
                    </div>
                    <!-- END Side Header -->

                    <!-- Side Main Navigation -->
                    <div class="content-side content-side-full">
                        <!--
                        Mobile navigation, desktop navigation can be found in #page-header

                        If you would like to use the same navigation in both mobiles and desktops, you can use exactly the same markup inside sidebar and header navigation ul lists
                        -->
                        <ul class="nav-main">
                            <li>
                                <a class="nav-submenu" data-toggle="nav-submenu" href="#">
                                    <i class="si si-calendar"></i> {!! trans('general.calendar') !!}
                                </a>
                                <ul>
                                    <li>
                                        <a href="{{ route('member.calendar') }}?event_type={!! \App\Enums\EventTypeEnum::ASSAIG!!}">{!! $colla->config->getTranslationAssaig()?: __('config.translation_assaig')  !!}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('member.calendar') }}?event_type={!! \App\Enums\EventTypeEnum::ACTUACIO!!}">{!!  $colla->config->getTranslationActuacio()?: __('config.translation_actuacio')  !!}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('member.calendar') }}?event_type={!! \App\Enums\EventTypeEnum::ACTIVITAT!!}">{!!  $colla->config->getTranslationActivitat()?: __('config.translation_activitat')  !!}</a>
                                    </li>
                                </ul>
                            </li>
                            @if(Auth::user()->casteller->getColla()->getConfig()->getBoardsEnabled())
                            <li>
                                <a class="nav-submenu" data-toggle="nav-submenu" href="pinyes">
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
                            <li>
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

                            <li>
                                <a class="nav-submenu" data-toggle="nav-submenu" href="#">
                                    @if( Auth::user()->casteller->getAlias() )
                                        <img src=" {!! Auth::user()->casteller->getProfileImage() !!}" class="img-avatar img-avatar32" alt="">
                                        <i class="fa fa-angle-down ml-5"></i>
                                    @endif
                                </a>
                                <ul>
                                    <li>
                                        @if( Auth::user()->casteller->getAlias() )
                                        <a class="dropdown-item" href="{{ route('member.profile') }}">
                                            {!! Auth::user()->casteller->getAlias() !!}
                                        </a>
                                        @endif
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">

                                            {!! trans('general.logout') !!}
                                        </a>
                                    </li>
                                    <form id="logout-form" action="{{ route('member.logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <!-- END Side Main Navigation -->
                </div>
                <!-- Sidebar Content -->
            </nav>
            <!-- END Sidebar -->
