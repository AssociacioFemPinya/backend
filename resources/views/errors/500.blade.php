@extends('errors.errors')

@section('content')
<div id="page-container" class="main-content-boxed">

    <!-- Main Container -->
    <main id="main-container">

        <!-- Page Content -->
        <div class="hero bg-white">
            <div class="hero-inner">
                <div class="content content-full">
                    <div class="py-30 text-center">
                        <h1 class="h2 font-w700 mt-30 mb-10">{!! trans('errors.error') !!}</h1>
                        <div class="display-3 text-info">
                            <i class="fa fa-lock"></i> 500
                        </div>
                        <h2 class="h3 font-w400 text-muted mb-50">{!! trans('errors.500') !!}</h2>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Page Content -->

    </main>
    <!-- END Main Container -->
</div>

@endsection
