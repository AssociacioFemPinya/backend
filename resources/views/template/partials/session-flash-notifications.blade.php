@if ($errors->any())
    <div class="alert alert-danger alert-dismissable" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><span aria-hidden="true">×</span></button>
        <h4 class="alert-heading font-size-h4 font-w600">Error</h4>
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

@if (Session::has('status_ok'))
    <div class="alert alert-success alert-dismissable" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
        <h4 class="alert-heading font-size-h4 font-w600">{!! trans('general.done') !!}</h4>
        <p class="mb-0">{!! Session::get('status_ok')  !!}</p>
    </div>
@endif

@if (Session::has('status_ko'))
    <div class="alert alert-danger alert-dismissable" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
        <h4 class="alert-heading font-size-h4 font-w600">{!! trans('general.error') !!}</h4>
        <p class="mb-0">{!! Session::get('status_ko') !!}</p>
    </div>
@endif
