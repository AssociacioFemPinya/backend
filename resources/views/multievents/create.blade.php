@extends('template.main')

@section('title', trans('general.multievents'))
@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
    <link rel="stylesheet" href="{!! asset('js/plugins/cloudflare-switchery/css/switchery.min.css') !!}">
@endsection

@section('content')

<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title">
            @if(isset($multievent))
                <b>{!! trans('multievent.update_multievent') !!}:</b> {!! $multievent->getName() !!}
            @else
                <b>{!! trans('multievent.add_new_multievent') !!}</b>
            @endif
        </h3>
    </div>
    <div class="block-content">
        @if(isset($multievent))
            {!! Form::open(array('id' => 'FormUpdateMultievent', 'url' => route('multievents.update', $multievent->getId()), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
        @else
            {!! Form::open(array('id' => 'FormAddMultievent', 'url' => route('multievents.add'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
        @endif
        
        <div class="row form-group">
            <div class="col-md-4">
                <label class="control-label">{!! trans('casteller.name') !!}</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="{!! trans('multievent.multievent_name') !!}" value="@if(isset($multievent)){!! old('name',$multievent->getName()) !!}@else {!! old('name') !!} @endif" required>
            </div>
            <div class="col-md-2">
                <label class="control-label">{!! trans('multievent.type') !!}</label>
                <select class="form-control" name="type" id="type">
                    @php $oldType = isset($multievent) ? $multievent->getType() : null; @endphp
                    @foreach ($types as $num => $type)
                        @if (isset($multievent))
                            <option value="{{ $num }}" @if(old('type', $oldType) == $num) selected @endif>{{ $type }}</option>
                        @else
                            <option value="{{ $num }}" @if(old('type') == $num) selected @endif>{{ $type }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="control-label">{!! trans('multievent.multievent_tags') !!}</label>
                <select class="selectize2 form-control" placeholder="{!! trans('general.tags') !!}" name="tags[]" style="width: 100%" multiple>
                    @php 
                        $oldTags = isset($multievent) ? 
                            (method_exists($multievent, 'tagsArray') ? $multievent->tagsArray('value') : 
                                ($multievent->getEvents()->isNotEmpty() ? $multievent->getEvents()->first()->tagsArray('value') : []))
                            : [];
                    @endphp
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->getValue() }}" @if(in_array($tag->getValue(), old('tags',$oldTags))) selected @endif>{{ $tag->getName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="control-label">{!! trans('multievent.multievent_tags_casteller') !!}</label>
                <select class="selectize2 form-control" placeholder="{!! trans('general.tags') !!}" name="tags_casteller[]" style="width: 100%" multiple>
                    @php 
                        $oldCastellerTags = isset($multievent) && $multievent->getEvents()->isNotEmpty() ? $multievent->getEvents()->first()->castellerTagsArray('value') : []; 
                    @endphp
                    @foreach ($tags_casteller as $tag)
                        <option value="{{ $tag->getValue() }}" @if(in_array($tag->getValue(), old('tags_casteller',$oldCastellerTags))) selected @endif>{{ $tag->getName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-4">
                <label class="control-label">{!! trans('general.date') !!}</label>
                <div class="row">
                    <div class="col-md-5">
                        <p class="text-muted">{!! trans('multievent.select_multiple_dates') !!}</p>
                        <p><span style="font-weight: 100; font-size: 60px;" class="num-events">@if(isset($multievent)){{ count($multievent->getEvents()) }}@else 0 @endif</span><br/> {!! trans('multievent.num_selected_dates') !!} </p>
                    </div>
                    <div class="col-md-7">
                        <div id="datepicker_start_date"></div>
                        <input type="hidden" name="event_dates" id="event_dates" value="@if(isset($multievent)){{ implode(',', $multievent->getEvents()->pluck('start_date')->map(function($date) { return date('d/m/Y', strtotime($date)); })->toArray()) }}@else{{ old('event_dates', '') }}@endif" required>
                        <!-- Removed start_date field as it's not needed for multievents -->
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label class="control-label">{!! trans('multievent.hour') !!}</label>
                <div class="row">
                    <div class="col-md-5">
                        <select class="form-control" name="hour" id="hour" required>
                            @php 
                                $oldHour = isset($multievent) && $multievent->getTime() ? $multievent->getHour() : null;
                            @endphp
                            @for ($i = 0; $i <= 23; $i++)
                                @php $hour = str_pad($i, 2, '0', STR_PAD_LEFT) @endphp
                                @if(isset($multievent))
                                    <option value="{!! $hour !!}" @if( old('hour', $oldHour) == $hour) selected @endif>{!! $hour !!}</option>
                                @else
                                    <option value="{!! $hour !!}" @if( old('hour') == $hour) selected @endif>{!! $hour !!}</option>
                                @endif
                            @endfor
                        </select>
                    </div>
                    <div class="col-sm-1 text-center" style="padding-left: 0; padding-right: 0; max-width: 1%;"><b>:</b></div>
                    <div class="col-5">
                        <select class="form-control" name="min" id="min" required>
                            @php 
                                $oldMinute = isset($multievent) && $multievent->getTime() ? $multievent->getMinute() : null;
                            @endphp
                            @for($i = 0; $i < 60; $i+=5)
                                @php $minute = str_pad($i, 2, '0', STR_PAD_LEFT) @endphp
                                @if(isset($multievent))
                                    <option value="{!! $minute !!}" @if( old('min', $oldMinute) == $minute) selected @endif>{!! $minute !!}</option>
                                @else
                                    <option value="{!! $minute !!}" @if( old('min') == $minute) selected @endif>{!! $minute !!}</option>
                                @endif
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <label class="control-label">{!! trans('multievent.duration') !!}</label>
                <input type="number" class="form-control" name="duration" id="duration" placeholder="00" value="@if(isset($multievent)){!! old('duration', $multievent->getDuration() )!!}@else{!! old('duration') !!}@endif" required>
            </div>
            <div class="col-md-1">
                <label class="control-label">{!! trans('multievent.visibility') !!}</label>
                <div class="visibility visible-lg">
                    @php
                        if (Session::has('status_ko') || $errors->any()) $oldVisible = ((old('visibility') !==  null )) ? 1 : 0;
                        else $oldVisible = isset($multievent) ? $multievent->getVisibility() : 1;
                    @endphp
                    @if(isset($multievent))
                        {!!  \App\Helpers\RenderHelper::fieldSwitcher($oldVisible,'data-id_visibility',$multievent->getId(),'visibility','visibility') !!}
                    @else
                        {!!  \App\Helpers\RenderHelper::fieldSwitcher($oldVisible,'data-id_visibility',null,'visibility','visibility') !!}
                    @endif
                </div>
            </div>
            <div class="col-md-2">
                <label class="control-label">{!! trans('multievent.companions') !!}</label>
                <div class="visible-lg">
                    @php
                        if (Session::has('status_ko') || $errors->any()) $oldCompanions = ((old('companions') !==  null )) ? 1 : 0;
                        else $oldCompanions = isset($multievent) ? $multievent->getCompanions() : 0;
                    @endphp
                    @if(isset($multievent))
                        {!!  \App\Helpers\RenderHelper::fieldSwitcher($oldCompanions,'data-id_companions',$multievent->getId(),'companions','companions') !!}
                    @else
                        {!!  \App\Helpers\RenderHelper::fieldSwitcher($oldCompanions,'data-id_companions',null,'companions','companions') !!}
                    @endif
                </div>
            </div>
        </div>

        <div class="row form-group" id="div_open_close_dates">
            <div class="col-md-6">
                <label class="control-label">{!! trans('multievent.open_date') !!}</label>
                <div class="row">
                    <div class="col-md-4">
                        <select class="form-control" name="open_date_select" id="open_date_select" required>
                            @php
                                $openDateSelect = "before_starts";
                                if(isset($multievent) && $multievent->getEvents()->isNotEmpty()) {
                                    $firstEvent = $multievent->getEvents()->first();
                                    if($firstEvent && $firstEvent->getOpenDate() && $firstEvent->getOpenDate()->isToday()) {
                                        $openDateSelect = "now";
                                    }
                                }
                            @endphp
                            <option value="before_starts" @if(old('open_date_select', $openDateSelect) == "before_starts") selected @endif>{!! trans('multievent.before_starts') !!}</option>
                            <option value="now" @if(old('open_date_select', $openDateSelect) == "now") selected @endif>{!! trans('multievent.immediately') !!}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="open_date_time" id="open_date_time" required>
                            @php
                                $openDateTime = 0;
                                if(isset($multievent) && $multievent->getEvents()->isNotEmpty()) {
                                    $firstEvent = $multievent->getEvents()->first();
                                    if($firstEvent && $firstEvent->getOpenDate() && $firstEvent->getStartDate() && $openDateSelect == "before_starts") {
                                        $diff = $firstEvent->getStartDate()->diffInHours($firstEvent->getOpenDate());
                                        $openDateTime = min(10, $diff); // Cap at 10
                                    }
                                }
                            @endphp
                            @for($i = 0; $i < 11; $i++)
                                <option value="{!! $i !!}" @if(old('open_date_time', $openDateTime) == $i) selected @endif>{!! $i !!}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" name="open_date_mode" id="open_date_mode" required>
                            @php
                                $openDateMode = "days";
                                if(isset($multievent) && $multievent->getEvents()->isNotEmpty()) {
                                    $firstEvent = $multievent->getEvents()->first();
                                    if($firstEvent && $firstEvent->getOpenDate() && $firstEvent->getStartDate()) {
                                        $diffHours = $firstEvent->getStartDate()->diffInHours($firstEvent->getOpenDate());
                                        $diffDays = $firstEvent->getStartDate()->diffInDays($firstEvent->getOpenDate());
                                        $diffWeeks = $firstEvent->getStartDate()->diffInWeeks($firstEvent->getOpenDate());
                                        $diffMonths = $firstEvent->getStartDate()->diffInMonths($firstEvent->getOpenDate());
                                        
                                        if($diffMonths >= 1) $openDateMode = "months";
                                        elseif($diffWeeks >= 1) $openDateMode = "weeks";
                                        elseif($diffDays >= 1) $openDateMode = "days";
                                        else $openDateMode = "hours";
                                    }
                                }
                            @endphp
                            <option value="hours" @if(old('open_date_mode', $openDateMode) == "hours") selected @endif>{!! trans('multievent.hours_before') !!}</option>
                            <option value="days" @if(old('open_date_mode', $openDateMode) == "days") selected @endif>{!! trans('multievent.days_before') !!}</option>
                            <option value="weeks" @if(old('open_date_mode', $openDateMode) == "weeks") selected @endif>{!! trans('multievent.weeks_before') !!}</option>
                            <option value="months" @if(old('open_date_mode', $openDateMode) == "months") selected @endif>{!! trans('multievent.months_before') !!}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label class="control-label">{!! trans('multievent.close_date') !!}</label>
                <div class="row">
                    <div class="col-md-4">
                        <select class="form-control" name="close_date_select" id="close_date_select" required>
                            @php
                                $closeDateSelect = "before_starts";
                                if(isset($multievent) && $multievent->getEvents()->isNotEmpty()) {
                                    $firstEvent = $multievent->getEvents()->first();
                                    if($firstEvent && $firstEvent->getCloseDate() && $firstEvent->getStartDate() && 
                                       $firstEvent->getCloseDate()->format('Y-m-d H:i:s') == $firstEvent->getStartDate()->format('Y-m-d H:i:s')) {
                                        $closeDateSelect = "when_starts";
                                    }
                                }
                            @endphp
                            <option value="before_starts" @if(old('close_date_select', $closeDateSelect) == "before_starts") selected @endif>{!! trans('multievent.before_starts') !!}</option>
                            <option value="when_starts" @if(old('close_date_select', $closeDateSelect) == "when_starts") selected @endif>{!! trans('multievent.when_starts') !!}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="close_date_time" id="close_date_time" required>
                            @php
                                $closeDateTime = 0;
                                if(isset($multievent) && $multievent->getEvents()->isNotEmpty()) {
                                    $firstEvent = $multievent->getEvents()->first();
                                    if($firstEvent && $firstEvent->getCloseDate() && $firstEvent->getStartDate() && $closeDateSelect == "before_starts") {
                                        $diff = $firstEvent->getStartDate()->diffInHours($firstEvent->getCloseDate());
                                        $closeDateTime = min(10, $diff); // Cap at 10
                                    }
                                }
                            @endphp
                            @for($i = 0; $i < 11; $i++)
                                <option value="{!! $i !!}" @if(old('close_date_time', $closeDateTime) == $i) selected @endif>{!! $i !!}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" name="close_date_mode" id="close_date_mode" required>
                            @php
                                $closeDateMode = "hours";
                                if(isset($multievent) && $multievent->getEvents()->isNotEmpty()) {
                                    $firstEvent = $multievent->getEvents()->first();
                                    if($firstEvent && $firstEvent->getCloseDate() && $firstEvent->getStartDate()) {
                                        $diffHours = $firstEvent->getStartDate()->diffInHours($firstEvent->getCloseDate());
                                        $diffDays = $firstEvent->getStartDate()->diffInDays($firstEvent->getCloseDate());
                                        $diffWeeks = $firstEvent->getStartDate()->diffInWeeks($firstEvent->getCloseDate());
                                        $diffMonths = $firstEvent->getStartDate()->diffInMonths($firstEvent->getCloseDate());
                                        
                                        if($diffMonths >= 1) $closeDateMode = "months";
                                        elseif($diffWeeks >= 1) $closeDateMode = "weeks";
                                        elseif($diffDays >= 1) $closeDateMode = "days";
                                        else $closeDateMode = "hours";
                                    }
                                }
                            @endphp
                            <option value="hours" @if(old('close_date_mode', $closeDateMode) == "hours") selected @endif>{!! trans('multievent.hours_before') !!}</option>
                            <option value="days" @if(old('close_date_mode', $closeDateMode) == "days") selected @endif>{!! trans('multievent.days_before') !!}</option>
                            <option value="weeks" @if(old('close_date_mode', $closeDateMode) == "weeks") selected @endif>{!! trans('multievent.weeks_before') !!}</option>
                            <option value="months" @if(old('close_date_mode', $closeDateMode) == "months") selected @endif>{!! trans('multievent.months_before') !!}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row form-group">
            <div class="col-md-6">
                <label class="control-label">{!! trans('attendance.attendance_answers') !!}</label>
                <select class="selectize2 form-control" placeholder="{!! trans('general.tags') !!}" name="answers[]" style="width: 100%" multiple>
                    @php $oldAnswers = isset($multievent) && $multievent->getEvents()->isNotEmpty() ? $multievent->getEvents()->first()->answersArray('VALUE') : []; @endphp
                    @foreach ($attendance_answers as $answer)
                        <option value="{{ $answer->getValue() }}" @if(in_array($answer->getValue(), old('answers',$oldAnswers))) selected @endif>{{ $answer->getName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="control-label">{!! trans('casteller.address') !!}</label>
                <textarea class="form-control" name="address" id="address" cols="10" rows="5">@if(isset($multievent)){!! old('address',$multievent->getAddress()) !!}@else{!! old('address') !!}@endif</textarea>
            </div>
            <div class="col-md-6">
                <label class="control-label">{!! trans('multievent.location_link') !!}</label>
                <textarea class="form-control" name="location_link" id="location_link" cols="1" rows="1">@if(isset($multievent)){!! old('location_link',$multievent->getLocationLink()) !!}@else{!! old('location_link') !!}@endif</textarea>
            </div>
            <div class="col-md-6">
                <label class="control-label">{!! trans('casteller.additional_information') !!}</label>
                <textarea class="form-control" name="comments" id="comments" cols="10" rows="5">@if(isset($multievent)){!! old('comments',$multievent->getComments()) !!}@else{!! old('comments') !!}@endif</textarea>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-6 text-left">
                <button class="btn btn-primary" href=""><i class="fa fa-save"></i> {!! trans('general.save') !!}</button>
                <a href="{{ route('multievents.list') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> {!! trans('general.back') !!} </a>
            </div>
            <div class="col-md-6 text-right">
                @if(isset($multievent))
                    <button class="btn btn-danger btn-delete-multievent" data-id_multievent="{!! $multievent->getId() !!}"><i class="fa fa-trash-o"></i> {!! trans('general.delete') !!}</button>
                @endif
            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>

<!-- START - MODAL DELETE MULTIEVENT -->
<div class="modal fade" id="modalDelMultievent" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-popin" role="document">
        <div class="modal-content">
            {!! Form::open(array('url' => 'n', 'method' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'fromDelMultievent')) !!}
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">{!! trans('general.delete') !!}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>

                <div class="block-content text-center">
                    <i class="fa fa-warning" style="font-size: 46px;"></i>
                    <h3 class="semibold modal-title text-danger">{!! trans('general.caution') !!}</h3>
                    <p class="text-muted">{!! trans('multievent.del_multievent_warning') !!}</p>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::submit(trans('general.delete') , array('class' => 'btn btn-danger')) !!}
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--/ END - MODAL DELETE MULTIEVENT -->

@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <script src="{{ asset('js/plugins/cloudflare-switchery/js/switchery.js') }}"></script>

    <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    @if (Auth()->user()->language=='ca')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.ca.min.js') !!}"></script>
    @elseif(Auth()->user()->language=='es')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js') !!}"></script>
    @endif

    <script>
        $(function ()
        {
            let elems = Array.prototype.slice.call(document.querySelectorAll('.js-switchery'));
            elems.forEach(function (html) {
                new Switchery(html, {size: 'small'});
            });

            $('.selectize2').select2({language: "ca"});

            // Button to delete multievent
            $('.btn-delete-multievent').on('click', function (e)
            {
                e.preventDefault();

                var id_multievent = $(this).data().id_multievent;
                $('#modalDelMultievent').modal('show');

                var url = "{{ route('multievents.destroy', ':id_multievent') }}";
                url = url.replace(':id_multievent', id_multievent);

                $('#fromDelMultievent').attr('action', url);
            });

            // Initialize datepicker for multievent
            var initDates = [];
            @if(isset($multievent))
                @foreach($multievent->getEvents() as $event)
                    initDates.push("{{ date('d/m/Y', strtotime($event->getStartDate())) }}");
                @endforeach
            @endif

            console.log(initDates);

            var datepicker = $("#datepicker_start_date").datepicker({
                @if (Auth()->user()->language=='ca')
                language: 'ca',
                @elseif(Auth()->user()->language=='es')
                language: 'es',
                @endif
                todayHighlight: true,
                multidate: true,
                format: "dd/mm/yyyy"
            });

            // Si estamos editando un multievento, seleccionar las fechas existentes
            if (initDates.length > 0) {
                // Convertir las fechas de formato dd/mm/yyyy a objetos Date
                var dateArray = initDates.map(function(dateStr) {
                    var parts = dateStr.split("/");
                    // Crear un objeto Date con partes de la fecha (año, mes-1, día)
                    return new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
                });
                
                // Establecer las fechas seleccionadas en el datepicker
                datepicker.datepicker('setDates', dateArray);
                console.log(dateArray);
                
                // Actualizar el campo oculto con las fechas formateadas
                var formattedDates = dateArray.map(function(date) {
                    var day = ('0' + date.getDate()).slice(-2);
                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                    var year = date.getFullYear();
                    return day + '/' + month + '/' + year;
                }).join(',');
                
                $("#event_dates").val(formattedDates);
                $('.num-events').html(dateArray.length);
                
                // Si hay fechas seleccionadas, establecer la primera como fecha principal
                if (dateArray.length > 0) {
                    var firstDate = dateArray[0];
                    var day = ('0' + firstDate.getDate()).slice(-2);
                    var month = ('0' + (firstDate.getMonth() + 1)).slice(-2);
                    var year = firstDate.getFullYear();
                    $("#start_date").val(day + '/' + month + '/' + year);
                }
            }

            // Update the hidden field when dates are selected
            $("#datepicker_start_date").on('changeDate', function(e) {
                var dates = $(this).datepicker('getDates');
                var formattedDates = dates.map(function(date) {
                    var day = ('0' + date.getDate()).slice(-2);
                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                    var year = date.getFullYear();
                    return day + '/' + month + '/' + year;
                }).join(',');

                $("#event_dates").val(formattedDates);
                $('.num-events').html(dates.length);

                // If there are dates selected, set the first as the main date of the multievent
                if (dates.length > 0) {
                    var firstDate = dates[0];
                    var day = ('0' + firstDate.getDate()).slice(-2);
                    var month = ('0' + (firstDate.getMonth() + 1)).slice(-2);
                    var year = firstDate.getFullYear();
                    $("#start_date").val(day + '/' + month + '/' + year);
                }
            });

            // Configure behavior for open/close dates
            $('#open_date_select').on('change', function () {
                var val = $('#open_date_select').val();

                if(val==='now') {
                    $('#open_date_time').hide();
                    $('#open_date_mode').hide();

                    $('#open_date_time').prop('required', false);
                    $('#open_date_mode').prop('required', false);
                } else {
                    $('#open_date_time').show();
                    $('#open_date_mode').show();

                    $('#open_date_time').prop('required', true);
                    $('#open_date_mode').prop('required', true);
                }
            });

            $('#close_date_select').on('change', function () {
                var val = $('#close_date_select').val();

                if(val==='when_starts') {
                    $('#close_date_time').hide();
                    $('#close_date_mode').hide();

                    $('#close_date_time').prop('required', false);
                    $('#close_date_mode').prop('required', false);
                } else {
                    $('#close_date_time').show();
                    $('#close_date_mode').show();

                    $('#close_date_time').prop('required', true);
                    $('#close_date_mode').prop('required', true);
                }
            });

            // Function to control visibility of dates based on the visibility switch
            function checkVisibility() {
                let visibility = document.querySelector('.visibility.visible-lg .js-switchery');

                if(!visibility.checked) {
                    $('#div_open_close_dates').hide();
                    $('#open_date_time').prop('required', false);
                    $('#open_date_mode').prop('required', false);
                    $('#close_date_time').prop('required', false);
                    $('#close_date_mode').prop('required', false);
                } else {
                    $('#div_open_close_dates').show();
                    $('#open_date_time').prop('required', true);
                    $('#open_date_mode').prop('required', true);
                    $('#close_date_time').prop('required', true);
                    $('#close_date_mode').prop('required', true);
                }
            }

            // Handle changes in the visibility switch
            $('.visibility.visible-lg').on('change', '.js-switchery', function () {
                checkVisibility();
            });

            checkVisibility();
            $('#close_date_select').change();
            $('#open_date_select').change();
        });
    </script>
@endsection