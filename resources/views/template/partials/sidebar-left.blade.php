<!-- Sidebar -->
    @if(Auth::user()->getColla()->getColor() !=='#000000')
        {{--  <nav id="sidebar"  class="sidebar" style=" background-color:{{Auth::user()->getColla()->getColor()}};">  --}}
        <nav id="sidebar">
    @else
        <nav id="sidebar">
    @endif

        <!-- Sidebar Content -->
    <div class="sidebar-content">
        <!-- Side Header -->
        <div class="content-header content-header-fullrow px-15">
            <!-- Mini Mode -->
            <div class="content-header-section sidebar-mini-visible-b">
                <!-- Logo -->
                <a class=" font-w700" href="{!! route('home') !!}">
                    <img class="pb-5" src="{!! asset('media/img/logo.svg') !!}" alt="FemPinya" width="60">
                </a>
                <!-- END Logo -->
            </div>
            <!-- END Mini Mode -->

            <!-- Normal Mode -->
            <div class="content-header-section text-center align-parent sidebar-mini-hidden">
                <!-- Close Sidebar, Visible only on mobile screens -->
                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                <button type="button" class="btn btn-circle btn-dual-secondary d-lg-none align-v-r" data-toggle="layout" data-action="sidebar_close">
                    <i class="fa-solid fa-close text-danger"></i>
                </button>
                <!-- END Close Sidebar -->

                <!-- Logo -->
                <div class="content-header-item">
                    <a class=" font-w700" href="{!! route('home') !!}">
                        @if(Auth::user()->getColla()->getLogo() )
                            <img class="img-avatar img-avatar64" src={{ asset('media/colles/'.Auth::user()->getColla()->getShortName().'/'.Auth::user()->getColla()->getLogo()) }} width="60" alt="{{ Auth::user()->getColla()->getName() }}" />
                            <br>
                            <span class="font-size-xl text-dual-primary-dark">{{Auth::user()->getColla()->getName()}}</span>
                        @else
                            <img class="img-avatar img-avatar64" src="{{ asset('media/img/logo.svg') }}" width="60" alt="" />
                            <br>
                            <span class="font-size-xl text-dual-primary-dark">Fem</span><span class="font-size-xl text-warning-light">Pinya</span>
                        @endif
                    </a>
                </div>
                <!-- END Logo -->
            </div>
            <!-- END Normal Mode -->
        </div>
        <!-- END Side Header -->

        <!-- Side Navigation -->
        <div class="content-side content-side-full mt-100">
                {{-- <div class="content-side content-side-full mt-50" style=" background-color:{{Auth::user()->getColla()->getColor()}};"> --}}
            <ul class="nav-main ">

                @can('admin')
                    {{-- START: ADMIN --}}
                    <li @if(Request::segment(1)=='admin') class="open" @endif>

                        @if(Request::segment(1)=='admin')
                            <a class="active nav-submenu" data-toggle="nav-submenu" href="#">
                        @else
                            <a class="nav-submenu" data-toggle="nav-submenu" href="#">
                        @endif

                            <i class="fa-solid fa-lock fa-fw"></i>
                            <span class="sidebar-mini-hide">{!! trans('admin.administrators') !!}</span>
                        </a>
                        {{--  <ul class="sidebar" style=" background-color:{{Auth::user()->getColla()->getColor()}}; filter: invert(25%); -webkit-filter: invert(25%);">  --}}
                        <ul>
                                <li>
                                <a @if(Request::segment(2)=='colles') class="active" @endif href="{!! route('admin.colles') !!}">{!! trans('admin.colles') !!}</a>
                            </li>
                            <li>
                                <a @if(Request::segment(2)=='users') class="active" @endif href="{!! route('admin.users') !!}">{!! trans('general.users') !!}</a>
                            </li>
                        </ul>
                    </li>
                    {{-- END: ADMIN --}}
                @endcan



                @canany(['view BBDD','view casteller config'])
                    {{-- START: CASTELLERS --}}
                    <li @if(Request::segment(1)=='castellers') class="open" @endif>

                        @if(Request::segment(1)=='castellers')
                            <a class="active nav-submenu" data-toggle="nav-submenu" href="{!! route('castellers.list') !!}">
                        @else
                            <a class="nav-submenu" data-toggle="nav-submenu" href="{!! route('castellers.list') !!}">
                        @endif
                            <i class="fa-regular fa-address-card fa-fw"></i>
                            <span class="sidebar-mini-hide">{!! trans('general.bbdd') !!}</span>
                            </a>
                            {{--  <ul class="sidebar" style=" background-color:{{Auth::user()->getColla()->getColor()}}; filter: invert(25%); -webkit-filter: invert(25%);">  --}}
                            <ul>
                                @can ('view BBDD')
                                    <li>
                                        <a @if((Request::segment(1)=='castellers' && Request::segment(2)=='list') || (Request::segment(1)=='castellers' && Request::segment(2)=='edit')) class="active" @endif href="{!! route('castellers.list') !!}">{!! trans('general.castellers') !!}</a>
                                    </li>
                                    <li>
                                        <a @if(Request::segment(1)=='castellers' && Request::segment(2)=='tags') class="active" @endif href="{!! route('castellers.tags') !!}">{!! (Auth::user()->getColla()->getConfig()->getBoardsEnabled()) ? trans('general.tags_and_positions') : trans('general.tags')!!}</a>
                                    </li>
                                @endcan

                                @can('view casteller config')
                                    <li>
                                        <a @if(Request::segment(1)=='castellers' && Request::segment(2)=='config') class="active" @endif href="{!! route('castellers.config.list') !!}">{!! trans('general.config') !!}</a>
                                    </li>
                                @endcan
                            </ul>
                    </li>
                    {{-- END: CASTELLERS --}}
                @endcanany


                @can ('view events')
                    {{-- START: EVENTS --}}

                        <li @if(Request::segment(1)=='events') class="open" @endif>

                            @if(Request::segment(1)=='events')
                                <a class="active nav-submenu" data-toggle="nav-submenu" href="{!! route('events.list') !!}">
                            @else
                                <a class="nav-submenu" data-toggle="nav-submenu" href="{!! route('events.list') !!}">
                            @endif
                                <i class="fa-regular fa-calendar-days fa-fw"></i>
                                <span class="sidebar-mini-hide">{!! trans('general.calendar') !!}</span>
                                </a>

                                <ul>
                                    <li>
                                        <a @if(Request::segment(1)=='events' && Request::segment(2)=='list') class="active" @endif  href="{!! route('events.list') !!}"><span class="sidebar-mini-hide">{!! trans('general.events') !!}</span></a>
                                    </li>
                                    <li>
                                        {{--  <a @if(Request::segment(2)=='events') class="active" @endif href="{!! route('admin.users') !!}">{!! trans('general.users') !!}</a>  --}}
                                        <a @if(Request::segment(1)=='events' && Request::segment(2)=='answers') class="active" @endif href="{!! route('events.answers') !!}">{!! trans('attendance.attendance_answers') !!}</a>
                                    </li>
                                    <li>
                                        <a @if(Request::segment(1)=='events' && Request::segment(2)=='tags') class="active" @endif href="{!! route('events.tags') !!}"> {!! trans('general.tags') !!}</a>
                                    </li>

                                </ul>
                        </li>
                    {{-- END: EVENTS --}}
                @endcan
                @if(Auth::user()->getColla()->getConfig()->getBoardsEnabled())
                    @can ('view boards')

                    <li @if(Request::segment(1)=='boards') class="open" @endif>

                        @if(Request::segment(1)=='boards')
                            <a class="active nav-submenu" data-toggle="nav-submenu" href="{!! route('boards.list') !!}">
                        @else
                            <a class="nav-submenu" data-toggle="nav-submenu" href="{!! route('boards.list') !!}">
                        @endif
                            <i class="fa-regular fa-map fa-fw"></i>
                            <span class="sidebar-mini-hide">{!! trans('general.templates') !!}</span>
                            </a>

                            <ul>
                                <li>
                                    <a @if(Request::segment(1)=='boards' && Request::segment(2)=='list') class="active" @endif  href="{!! route('boards.list') !!}"><span class="sidebar-mini-hide">{!! trans('general.templates') !!}</span></a>
                                </li>
                                <li>
                                    {{--  <a @if(Request::segment(2)=='events') class="active" @endif href="{!! route('admin.users') !!}">{!! trans('general.users') !!}</a>  --}}
                                    <a @if(Request::segment(1)=='boards' && Request::segment(2)=='biblio') class="active" @endif href="{!! route('boards.biblio') !!}">{!! trans('boards.biblio_title') !!}</a>
                                </li>
                                <li>
                                    <a @if(Request::segment(1)=='boards' && Request::segment(2)=='tags') class="active" @endif href="{!! route('boards.tags') !!}"> {!! trans('boards.bases') !!}</a>
                                </li>
                            </ul>
                    </li>
                    @endcan
                @endif

                @canany('view notifications','edit notifications')
                {{-- START: NOTIFICATIONS --}}


                <li @if(Request::segment(1)=='notifications') class="open" @endif>

                @if(Request::segment(1)=='notifications')
                    <a class="active nav-submenu" data-toggle="nav-submenu" href="{!! route('notifications.scheduled_notifications.list') !!}">
                @else
                    <a class="nav-submenu" data-toggle="nav-submenu" href="{!! route('notifications.scheduled_notifications.list') !!}">
                @endif
                    <i class="fa-regular fa-envelope fa-fw"></i>
                    <span class="sidebar-mini-hide">{!! trans('general.notifications') !!}</span>
                    </a>

                    <ul>
                        <li>
                            {{-- TODO POSAR ELS NOMS A TRANS --}}
                            <a @if(Request::segment(1)=='notifications' && Request::segment(2)=='scheduled') class="active" @endif  href="{!! route('notifications.scheduled_notifications.list') !!}"><span class="sidebar-mini-hide">{!! trans('notifications.scheduled_notifications') !!}</span></a>
                        </li>
                        <li>
                            <a @if(Request::segment(1)=='notifications' && Request::segment(2)=='messages') class="active" @endif href="{!! route('notifications.messages.list') !!}">{!! trans('notifications.messages') !!}</a>
                        </li>
                        <li>
                            <a @if(Request::segment(1)=='notifications' && Request::segment(2)=='reminders') class="active" @endif href="{!! route('notifications.reminders.list') !!}">{!! trans('notifications.reminders') !!}</a>
                        </li>
                        <li>
                            <a @if(Request::segment(1)=='notifications' && Request::segment(2)=='register') class="active" @endif href="{!! route('notifications.register.list') !!}">{!! trans('notifications.logs') !!}</a>
                        </li>
                    </ul>
                </li>


                <!-- <a class="nav-submenu" data-toggle="nav-submenu" href="{!! route('events.list') !!}">
                    <i class="si si-envelope"></i>
                    <span class="sidebar-mini-hide">{!! trans('general.notifications') !!}</span>
                </a>
                <ul>
                <li>
                                        <a @if(Request::segment(1)=='events' && Request::segment(2)=='list') class="active" @endif  href="{!! route('events.list') !!}"><span class="sidebar-mini-hide">{!! trans('general.events') !!}</span></a>
                                    </li>
                <li @if(Request::segment(1)=='notifications') class="open" @endif>
                    <a @if(Request::segment(1)=='notifications') class="active" @endif  href="{!! route('notifications.scheduled_notifications.list') !!}"><i class="si si-map"></i><span class="sidebar-mini-hide">{!! trans('general.notifications') !!}</span></a>
                </li>
                </ul> -->
                {{-- END: NOTIFICATIONS --}}
                @endcanany
            </ul>
        </div>

        </div>
            <!-- END Side Navigation -->
        <div class="content-header-section text-center align-parent sidebar-mini-hidden">
            <!-- Logo -->
            @if(Auth::user()->getColla()->getLogo() )
        <div class="content-header-item">
            <a class=" font-w700" href="{!! route('home') !!}">
                <img class="pb-5" src="{!! asset('media/img/logo.svg') !!}" alt="Logo: FemPinya" width="60">
                <br>
                <span class="font-size-xl text-dual-primary-dark">Fem</span><span class="font-size-xl text-warning-light">Pinya</span>
            </a>
        </div>
        @endif
        <!-- END Logo -->
        </div>


        <!-- Sidebar Content -->

    </nav>
<!-- END Sidebar -->
