<!-- Header -->
<header id="page-header">
    <!-- Header Content -->
    <div class="content-header" >

        <!-- Left Section -->
        <div class="content-header-section">
            <div class="content-header-item">
            <a class="link-effect font-w700" href="{!! route('events.list') !!}">
                    <i class="fa fa-fa-list-alt text-gray"></i>
                    <span class="font-size-xl text-dual-primary-dark"><< </span>
                </a>
            </div>
            <div class="content-header-item">
                <button type="button" class="btn btn-circle btn-dual-secondary" id="btnToggleSidebarLeft">
                    <i class="fa fa-outdent text-gray"></i>
                </button>
            </div>
            <!-- Logo -->
            <div class="content-header-item logo-container">
                <a class="link-effect font-w700" href="{!! route('home') !!}">
                    <img src="{!! asset('media/img/logo.svg') !!}" alt="FemPinya" style="width: 20px;" class="mb-2">
                    <span class="font-size-xl text-dual-primary-dark">Fem</span><span class="font-size-xl text-warning-light">Pinya</span>
                </a>
            </div>
            <!-- END Logo -->
        </div>
        <!-- END Left Section -->

        <!--Event Info Section -->
        <div class="content-header-section section-title-container">
            <div class="content-header-item">
                <h4 class="text-dual-primary-dark font-600 mb-0 mt-1">{!! $event->getName() !!} {!! $event->getStartDate()->format('d/m/Y') !!}</h4>
            </div>
        </div>
        <!--END Event Info Section -->

        <!--Event Info Section -->
        <div class="content-header-section">
            <button type="button" class="btn btn-circle btn-dual-secondary" data-toggle="layout" data-action="header_search_on">
                <i class="fa fa-search"></i>
            </button>
           <div class="btn-group" role="group">
               <button type="button" class="btn btn-circle btn-dual-secondary" id="page-header-options-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   <i class="fa fa-wrench"></i>
               </button>
               <div class="dropdown-menu min-width-300" id="optionsPanel" aria-labelledby="page-header-options-dropdown" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 34px, 0px); top: 0px; left: 0px; will-change: transform;">
                   <h5 class="h6 text-center py-10 mb-10 border-b text-uppercase">{!! trans('general.settings') !!}</h5>
                   <div class="row gutters-tiny text-center">
                       <div class="col-6">
                           <button class="dropdown-item mb-0" id="addFavourite">
                               <i id="iFavourite" class="fa @if($boardEvent->getFavourite()) text-warning fa-star @else fa-star-o @endif"></i> {!! trans('boards.add_favourite') !!}
                           </button>
                       </div>
                       <div class="col-6">
                           <button class="dropdown-item mb-0" id="toProject">
                               <i id="iDisplay" class="fa @if($boardEvent->getDisplay()) text-warning fa-eye @else fa-eye-slash @endif  mr-5"></i> {!! trans('boards.to_project') !!}
                           </button>
                       </div>

                   </div>
               </div>
           </div>
        </div>
        <!--END Event Info Section -->

        <!--Middle Section-->
        <div class="content-header-section">
            <!--Header Navigation-->
            <!-- <ul class="nav-main-header"> -->
            <ul class="nav-control-buttons">
                <li>
                    <a href="#" id="attachBoardOnEvent"><i class="fa fa-plus-circle"></i>{!! trans('event.add_pinya') !!}</a>
                </li>
                <li>
                    <span class="nav-submenu" href=""><img src="{{ asset('media/img/ico_pinya_o3.svg') }}" class="mr-5 mb-1" style="color: rgb(200, 208, 218); width: 15px;" alt="">{!! trans('event.boards_form_event') !!}</span>
                    <ul id="listboard">
                        @foreach($event->getBoardsEvent() as $boardEvent)
                            <li><a class="align-middle" style="height: 46px;" href="{{ route('event.board', ['event' => $event->getId(), 'boardEvent' => $boardEvent->getId()]) }}">{!! $boardEvent->getBoard()->getName() !!}<button class="btn btn-sm float-right btn-danger btn-delete-boardevent" data-id_boardevent='{{ $boardEvent->getId()}}' ><i class="fa fa-trash"></i></button></a></li>
                        @endforeach
                    </ul>
                </li>

            </ul>
        </div>
        <!--END Middle Section-->

        <!-- Right Section -->
        <div class="content-header-section user-btn">
            <!-- User Dropdown -->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-rounded btn-dual-secondary" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="{!! Auth::user()->getProfileImage() !!}" class="img-avatar img-avatar32" alt="">
                    <span class="profile-text">{{ Auth::user()->getName() }}</span>
                    <i class="fa fa-angle-down ml-5"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right min-width-150" aria-labelledby="page-header-user-dropdown">
                    <a class="dropdown-item" href="{{ route('profile.user') }}">
                        <i class="si si-user mr-5"></i> {!! trans('user.profile') !!}
                    </a>

                    @if(Auth::user()->can('view colla'))
                        <a class="dropdown-item" href="{!! route('profile.colla') !!}">
                            <i class="si si-wrench mr-5"></i> {!! trans('user.my_colla') !!}
                        </a>
                    @endif

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">

                        <i class="si si-logout mr-5"></i> {!! trans('general.logout') !!}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
            <!-- END User Dropdown -->

        </div>
        <!-- END Right Section -->

        <!-- START bottom mobile section -->

        <!-- END bottom mobile section -->
    </div>
    <!-- END Header Content -->
</header>
<!-- END Header -->
