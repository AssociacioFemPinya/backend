<div class="block block-themed block-transparent mb-0">
    <div class="block-header bg-primary-dark">
        <h3 class="block-title">

            @if(isset($period))
                {!! trans('period.update_period') !!}
            @else
                {!! trans('period.add_period') !!}
            @endif
        </h3>
        <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                <i class="si si-close"></i>
            </button>
        </div>
    </div>
    @if(isset($period))
        {!! Form::open(array('id' => 'FormUpdatePeriod', 'url' => route('profile.colla.periods.update', $period->getId()), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
    @else
        {!! Form::open(array('id' => 'FormAddPeriod', 'url' => route('profile.colla.periods.add'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
    @endif
    <div class="block-content">
        <div class="row form-group">
            <div class="col-md-12">
                <label class="control-label">{!! trans('period.name_period') !!}</label>
                <input type="text" class="form-control" id="name" name="name" value="@if(isset($period)){!! $period->getName() !!}@endif" required>
            </div>

        </div>
        <div class="row form-group">
            <div class="col-md-6">
                <label class="control-label">{!! trans('period.start_period') !!}</label>
                <input type="text" class="form-control datepicker" name="start_period" id="start_period" value="@if(isset($period)){!!date('d/m/Y', strtotime($period->start_period)) !!}@endif" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}"" required>
            </div>
            <div class="col-md-6">
                <label class="control-label">{!! trans('period.end_period') !!}</label>
                <input type="text" class="form-control datepicker" id="end_period" name="end_period" value="@if(isset($period)){!!date('d/m/Y', strtotime($period->end_period)) !!}@endif" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}"" required>
            </div>
        </div>



    </div>
</div>
<div class="modal-footer">
    @if(isset($period))
        <button type="submit" form="FormUpdatePeriod" class="btn btn-alt-primary">{!! trans('general.update') !!}</button>
    @else
        <button type="submit" form="FormAddPeriod" class="btn btn-alt-primary">{!! trans('general.save') !!}</button>
    @endif
    <button type="button" class="btn  btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
</div>
{!! Form::close() !!}

