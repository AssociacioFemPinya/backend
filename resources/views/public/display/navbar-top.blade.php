
<header id="page-header">
    <!-- Header Content -->
    <div  class="content-header">
        <!-- Left Section -->
        <div class="content-header-section">
            <!-- Logo -->
            <img class="pb-5" src="{!! asset('media/img/logo.svg') !!}" alt="Logo FemPinya" width="40">
             <!-- END Logo -->
        </div>
        <!-- END Left Section -->

        <!-- Center Section -->
        <div class="content-header-section">
            <b>{!! $collaName !!}</b>
        </div>
        <!-- END Center Section -->

        <!-- Right Section -->
        <div id="fixedbutton" class="btn btn-primary btn-board mr-1">
            <img src="<?php echo e(asset('media/img/ico_pinya_o3.svg')); ?>" style="width: 22px;" alt="" />
        </div>
        <div class="col-sm-6 col-md-6 col-lg-2 col-xl-2 pt-5">
            @if(isset($boardEvent) && ($board->hasFolre()))
                <select name="base" id="base" class="form-control">
                    <option value="{{ \App\Enums\BasesEnum::PINYA }}">{{ \App\Enums\BasesEnum::PINYA }}</option>
                    <option value="{{ \App\Enums\BasesEnum::FOLRE }}">{{ \App\Enums\BasesEnum::FOLRE }}</option>
                    @if($board->hasManilles())<option value="{{ \App\Enums\BasesEnum::MANILLES }}">{{ \App\Enums\BasesEnum::MANILLES }}</option>@endif
                    @if($board->hasPuntals())<option value="{{ \App\Enums\BasesEnum::PUNTALS }}">{{ \App\Enums\BasesEnum::PUNTALS }}</option>@endif
                </select>
            @endif
        </div>
        <div class="content-header-section">
            {!! $boardEventName !!}
        </div>
        <!-- END Right Section -->
    </div>
    <!-- END Header Content -->

</header>
<!-- END Header -->
