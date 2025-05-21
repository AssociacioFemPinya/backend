<!-- Header -->
<header id="page-header">
    <!-- Header Content -->
    <div class="content-header" >

        <!-- Left Section -->
        <div class="content-header-section section-logo-container">
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
                <h4 class="text-dual-primary-dark font-600 mb-0 mt-1">{!! $event->getName() !!} - {!! $event->getStartDate()->format('d/m/Y') !!} | {!! $boardEvent->getDisplayName() !!}</h4>
            </div>
        </div>
        <!--END Event Info Section -->
        <!--Middle Section-->
        <div class="content-header-section">
            <button type="button" class="btn btn-circle btn-dual-secondary" id="editBoardEvent" data-toggle="tooltip" data-html="true" data-placement="bottom" title="{!! trans('boards.tooltip_edit') !!}">
                <i class="fa fa-pencil"></i>
            </button>
            <button type="button" class="btn btn-circle btn-dual-secondary" id="toDisplay" data-toggle="tooltip" data-html="true" data-placement="bottom" title="{!! trans('boards.tooltip_to_display') !!}">
                <i id="iDisplay" class="fa @if($boardEvent->getDisplay()) text-warning fa-eye @else fa-eye-slash @endif"></i>
            </button>
            <button type="button" class="btn btn-circle btn-dual-secondary" id="addFavourite" data-toggle="tooltip" data-html="true" data-placement="bottom" title="{!! trans('boards.tooltip_add_favourite') !!}">
                <i id="iFavourite" class="fa @if($boardEvent->getFavourite()) text-warning fa-star @else fa-star-o @endif"></i>
            </button>
            <div id="listBoardsGroup" class="btn-group" role="group" data-toggle="tooltip" data-html="true" data-placement="bottom" title="{!! trans('boards.tooltip_switch_pinya') !!}">
                <button type="button" class="btn btn-circle btn-dual-secondary" id="boards-list" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                    <img src="{{ asset('media/img/ico_pinya_o3.svg') }}" style="color: rgb(200, 208, 218); width: 14px;" alt="">
                </button>
                <div id="listboard" class="row gutters-tiny dropdown-menu"  aria-labelledby="boards-list" x-placement="bottom-start">
                    <h5 class="h6 text-center py-10 mb-10 border-b text-uppercase">{!! trans('general.pinyes') !!}</h5>
                    @foreach($boardsInEvent as $boardInEvent)

                        @php($boardInEventId = $boardInEvent->getId())
                        @php($boardInEventDisplayName = $boardInEvent->getDisplayName())

                        <div class="col-12">

                            @if ($boardInEventId === $boardEventId )
                                <button class="dropdown-item mb-0 mr-15" disabled>
                                    {!! $boardInEventDisplayName !!}
                                </button>
                                <button class="btn btn-sm float-right btn-danger" disabled><i class="fa fa-trash"></i></button>
                            @else
                                <a class="dropdown-item mb-0 mr-15" href="{{ route('event.board', ['event' => $event->getId(), 'boardEvent' => $boardInEventId]) }}">
                                    {!! $boardInEventDisplayName !!}
                                </a>
                                <button class="btn btn-sm float-right btn-danger btn-delete-boardevent" data-id_boardevent='{{ $boardInEventId }}' ><i class="fa fa-trash"></i></button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <button type="button" class="btn btn-circle btn-dual-secondary" id="attachBoardOnEvent" data-toggle="tooltip" data-html="true" data-placement="bottom" title="{!! trans('boards.tooltip_attach_pinya') !!}">
                <i class="fa fa-plus-circle"></i>
            </button>
            <button type="button" class="btn btn-circle btn-dual-secondary" id="importBoardOnEvent" data-toggle="tooltip" data-html="true" data-placement="bottom" title="{!! trans('boards.tooltip_import_pinya') !!}">
                <i class="fa fa-cloud-upload"></i>
            </button>
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
