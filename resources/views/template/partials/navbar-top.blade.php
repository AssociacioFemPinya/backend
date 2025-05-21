<!-- Header -->
{{--  @if(Auth::user()->getColla()->getColor() !=='#000000')
    <header id="page-header"  style=" background-color:{{Auth::user()->getColla()->getColor()}};">
@else
    <header id="page-header">
@endif  --}}
<header id="page-header">
    <!-- Header Content -->
    <div  class="content-header" style="margin-right: 10px; margin-left: 10px; max-width: none;">
            <!-- Left Section -->
        <div class="content-header-section">
            <!-- Toggle Sidebar -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <button type="button" class="btn btn-circle btn-dual-secondary" data-toggle="layout" data-action="sidebar_toggle">
                <i class="fa fa-outdent"></i>
            </button>
            <!-- END Toggle Sidebar -->
        </div>
        <!-- END Left Section -->

        <!-- Center Section -->

        {{--  <div class="content">
            <div class="row gutters-tiny invisible" data-toggle="appear">
            <!-- Row  -->
                <div class="col-12 col-md-8 col-xl-12">
                    @if(Auth::user()->getColla()->getBanner() )
                        <a class="block block-transparent bg-image img-fluid" href="javascript:void(0)" style="position: absolute; top: -45px;  height:70px; width:100%; background-image: url('{{ asset('media/colles/'.Auth::user()->getColla()->getShortName().'/'.Auth::user()->getColla()->getBanner()) }}' );">
                            <div class="block-options block-options-center font-w600">
                                <h5 class="py-5 text-center"> {{ Auth::user()->getName() }} {{ Auth::user()->getColla()->getName() }}  &  FemPinya</h5>
                            </div>
                        </a>
                    @else
                        <a class="block block-transparent bg-image img-fluid" href="javascript:void(0)" style="position: absolute; top: -45px;  height:70px; width:100%; background-image: url('{!! asset('media/img/banner.png') !!}');">
                            <div class="block-options block-options-center font-w600">
                                <h5 class="text-center">Welcome admin!!! {{ Auth::user()->getName() }} de {{ Auth::user()->getColla()->getName() }}  &  FemPinya</h5>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>  --}}
        <!-- END Center Section -->

        <!-- Right Section -->
        <div class="content-header-section">
            <!-- User Dropdown -->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-rounded btn-dual-secondary" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="{!! Auth::user()->getProfileImage() !!}" class="img-avatar img-avatar32" alt="">
                    {{ Auth::user()->getName() }}<i class="fa fa-angle-down ml-5"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right min-width-150" aria-labelledby="page-header-user-dropdown">
                    <a class="dropdown-item" href="{{ route('profile.user') }}">
                        <i class="si si-user mr-5"></i> {!! trans('user.profile') !!}
                    </a>


                    @can('view colla')
                        <a class="dropdown-item" href="{!! route('profile.colla') !!}">
                            <i class="si si-wrench mr-5"></i> {!! trans('user.my_colla') !!}
                        </a>
                    @endcan


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
    </div>
    <!-- END Header Content -->

</header>
<!-- END Header -->
