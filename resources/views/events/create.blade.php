@extends('template.main')

@section('title', trans('general.attendance'))
@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
    <link rel="stylesheet" href="{!! asset('js/plugins/cloudflare-switchery/css/switchery.min.css') !!}">
@endsection

@section('content')

<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title">
            @if(isset($event))
                <b>{!! trans('event.update_event') !!}:</b> {!! $event->name !!} ({!! date('d/m/Y', strtotime($event->start_date)) !!})
            @else
                @if($type_add=='ONE')
                    <b>{!! trans('event.add_new_event') !!}</b>
                @elseif($type_add=='GROUP')
                    <b>{!! trans('event.add_new_group_events') !!}</b>
                @endif
            @endif
        </h3>
    </div>
    <div class="block-content">
        @if(isset($event))
            {!! Form::open(array('id' => 'FormUpdateEvent', 'url' => route('events.update', $event->id_event), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
        @else
            @if($type_add=='ONE')
                {!! Form::open(array('id' => 'FormAddEvent', 'url' => route('events.add'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
            @elseif($type_add=='GROUP')
                {!! Form::open(array('id' => 'FormAddEventGroup', 'url' => route('events.add-group'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
            @endif
        @endif
        <div class="row form-group">
            <div class="col-md-4">
                <label class="control-label">{!! trans('casteller.name') !!}</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="{!! trans('event.event_name') !!}" value="@if(isset($event)){!! old('name',$event->name) !!}@else {!! old('name') !!} @endif" required>
            </div>
            <div class="col-md-2">
                <label class="control-label">{!! trans('event.type') !!}</label>
                <select class="form-control" name="type" id="type">
                    @php $oldType = (isset($event)) ? $event->getType() : null ;@endphp
                    @php print_r($oldType); @endphp
                    @foreach ($types as $num => $type)
                        @if (isset($event))
                            <option value="{{ $num }}" @if(old('type', $oldType) == $num) selected @endif>{{ $type }}</option>
                        @else
                            <option value="{{ $num }}" @if(old('type') == $num) selected @endif>{{ $type }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="control-label">{!! trans('event.event_tags') !!}</label>
                <select class="selectize2 form-control" placeholder="{!! trans('general.tags') !!}" name="tags[]" style="width: 100%" multiple>
                    @php $oldTags = isset($event) ? $event->tagsArray('value') : []; @endphp
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->getValue() }}" @if(in_array($tag->getValue(), old('tags',$oldTags))) selected @endif>{{ $tag->getName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="control-label">{!! trans('event.event_tags_casteller') !!}</label>
                <select class="selectize2 form-control" placeholder="{!! trans('general.tags') !!}" name="tags_casteller[]" style="width: 100%" multiple>
                    @php $oldCastellerTags = isset($event) ? $event->castellerTagsArray('value') : []; @endphp
                    @foreach ($tags_casteller as $tag)
                        <option value="{{ $tag->getValue() }}" @if(in_array($tag->getValue(), old('tags_casteller',$oldCastellerTags))) selected @endif>{{ $tag->getName() }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="row form-group">
            @if(isset($event) || $type_add=='ONE')<div class="col-md-3">@elseif($type_add=='GROUP')<div class="col-md-4">@endif
                <label class="control-label">{!! trans('general.date') !!}</label>
                @if(isset($event) || $type_add=='ONE')
                    <input type="text" class="form-control" name="start_date" id="start_date" placeholder="{!! trans('event.event_date') !!}" value="@if(isset($event)){!! old('start_date', date('d/m/Y', strtotime($event->start_date))) !!}@else{!! old('start_date') !!}@endif" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" required>
                @elseif(!isset($event) && $type_add=='GROUP')
                    <div class="row">
                        <div class="col-md-5">
                            <p class="text-muted">{!! trans('event.select_multiple_dates') !!}</p>
                            <p><span style="font-weight: 100; font-size: 60px;" class="num-events">0</span><br/> {!! trans('event.num_selected_dates') !!} </p>
                        </div>
                        <div class="col-md-7">
                            <div id="datepicker_start_date"></div>
                            <input type="hidden" name="start_dates" id="start_dates" value="" required>
                        </div>
                    </div>
                @endif
            </div>
                <div class="col-md-3">
                <label class="control-label">{!! trans('event.hour') !!}</label>
                <div class="row">
                    <div class="col-md-5">
                        <select class="form-control" name="hour" id="hour" required>
                            @php $oldHour = (isset($event)) ? date('H', strtotime($event->getStartDate())) : null @endphp
                            @for ($i = 0; $i <= 23; $i++)
                                @php $hour = str_pad($i, 2, '0', STR_PAD_LEFT) @endphp
                                @if(isset($event))
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
                            @php $oldMinute = (isset($event)) ? date('i', strtotime($event->getStartDate())) : null @endphp
                            @for($i = 0; $i < 60; $i+=5)
                                @php $minute = str_pad($i, 2, '0', STR_PAD_LEFT) @endphp
                                @if(isset($event))
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
                <label class="control-label">{!! trans('event.duration') !!}</label>
                <input type="number" class="form-control" name="duration" id="duration" placeholder="00" value="@if(isset($event)){!! old('duration', $event->duration )!!}@else{!! old('duration') !!}@endif" required>
            </div>
            @if(isset($event) || $type_add=='ONE')<div class="col-md-2">@elseif(!isset($event) && $type_add=='GROUP')<div class="col-md-1">@endif
                <label class="control-label">{!! trans('event.visibility') !!}</label>
                <div class="visibility visible-lg">
                    @php
                        if (Session::has('status_ko') || $errors->any()) $oldVisible = ((old('visibility') !==  null )) ? 1 : 0;
                        else $oldVisible = (isset($event)) ? $event->getVisibility() : 1;
                    @endphp
                    @if(isset($event))
                        {!!  \App\Helpers\RenderHelper::fieldSwitcher($oldVisible,'data-id_visibility',$event->getId(),'visibility','visibility') !!}
                    @else
                        {!!  \App\Helpers\RenderHelper::fieldSwitcher($oldVisible,'data-id_visibility',null,'visibility','visibility') !!}
                    @endif
                </div>
            </div>
            <div class="col-md-2">
                <label class="control-label">{!! trans('event.companions') !!}</label>
                    <div class="visible-lg">
                        @php
                            if (Session::has('status_ko') || $errors->any()) $oldCompanions = ((old('companions') !==  null )) ? 1 : 0;
                            else $oldCompanions = (isset($event)) ? $event->getCompanions() : 0;
                        @endphp
                        @if(isset($event))
                            {!!  \App\Helpers\RenderHelper::fieldSwitcher($oldCompanions,'data-id_companions',$event->getId(),'companions','companions') !!}
                        @else
                            {!!  \App\Helpers\RenderHelper::fieldSwitcher($oldCompanions,'data-id_companions',null,'companions','companions') !!}
                        @endif
                    </div>
            </div>
        </div>
        @if(isset($event) || $type_add=='ONE')
                <div class="row form-group" id="div_open_close_dates">
                    <div class="col-md-6">
                        <label class="control-label">{!! trans('event.open_date') !!}</label>
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control" name="open_date_select" id="open_date_select">
                                    <option value="date" @if(old('open_date_select') == "date") selected @endif>{!! trans('general.select_date') !!}</option>
                                    <option value="before_starts" @if(old('open_date_select') == "before_starts") selected @endif>{!! trans('event.before_starts') !!}</option>
                                    <option value="now" @if(old('open_date_select') == "now") selected @endif>{!! trans('event.immediately') !!}</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="div_open_date">
                                <input type="text" class="form-control" name="open_date" id="open_date" placeholder="{!! trans('general.select_date') !!}" value="@if(isset($event)){!! old('open_date',date('d/m/Y', strtotime($event->open_date))) !!}@else{!! old('open_date') !!}@endif" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" required>
                            </div>
                            <div class="col-md-4" id="div_open_time">
                                <div class="row">
                                    <div class="col-5" style="padding-left: 0; padding-right: 5px;">
                                        <select class="form-control" name="hour_open_date" id="hour_open_date" required>
                                            @php $oldHour = (isset($event)) ? date('H', strtotime($event->open_date)) : null @endphp
                                            @for ($i = 0; $i <= 23; $i++)
                                                @php $hour = str_pad($i, 2, '0', STR_PAD_LEFT) @endphp
                                                @if(isset($event))
                                                    <option value="{!! $hour !!}" @if( old('hour_open_date', $oldHour) == $hour) selected @endif>{!! $hour !!}</option>
                                                @else
                                                    <option value="{!! $hour !!}" @if( old('hour_open_date') == $hour) selected @endif>{!! $hour !!}</option>
                                                @endif
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-sm-1 text-center" style="padding-left: 0; padding-right: 0; max-width: 1%;"><b>:</b></div>
                                    <div class="col-5" style="padding-left: 5px; padding-right: 0;">
                                        <select class="form-control" name="min_open_date" id="min_open_date" required>
                                            @php $oldMinute = (isset($event)) ? date('i', strtotime($event->open_date)) : null @endphp
                                            @for($i = 0; $i < 60; $i+=5)
                                                @php $minute = str_pad($i, 2, '0', STR_PAD_LEFT) @endphp
                                                @if(isset($event))
                                                    <option value="{!! $minute !!}" @if( old('min_open_date', $oldMinute) == $minute) selected @endif>{!! $minute !!}</option>
                                                @else
                                                    <option value="{!! $minute !!}" @if( old('min_open_date') == $minute) selected @endif>{!! $minute !!}</option>
                                                @endif
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="open_date_time" id="open_date_time" required>
                                    @for($i = 0; $i < 11; $i++)
                                    <option value="{!! $i !!}" @if( old('open_date_time') == $i) selected @endif>{!! $i !!}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" name="open_date_mode" id="open_date_mode" required>
                                    <option value="hours" @if( old('open_date_mode') == "hours") selected @endif>{!! trans('event.hours_before') !!}</option>
                                    <option value="days" @if( old('open_date_mode') == "days") selected @endif>{!! trans('event.days_before') !!}</option>
                                    <option value="weeks" @if( old('open_date_mode') == "weeks") selected @endif>{!! trans('event.weeks_before') !!}</option>
                                    <option value="months" @if( old('open_date_mode') == "months") selected @endif>{!! trans('event.months_before') !!}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">{!! trans('event.close_date') !!}</label>
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control" name="close_date_select" id="close_date_select">
                                    <option value="date" @if(old('close_date_select') == "date") selected @endif>{!! trans('general.select_date') !!}</option>
                                    <option value="before_starts" @if(old('close_date_select') == "before_starts") selected @endif>{!! trans('event.before_starts') !!}</option>
                                    <option value="when_starts" @if(old('close_date_select') == "when_starts") selected @endif>{!! trans('event.when_starts') !!}</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="div_close_date">
                                <input type="text" class="form-control" name="close_date" id="close_date" placeholder="{!! trans('general.select_date') !!}" value="@if(isset($event)){!! old('close_date',date('d/m/Y', strtotime($event->close_date))) !!}@else{!! old('close_date') !!}@endif" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" required>
                            </div>
                            <div class="col-md-4" id="div_close_time">
                                <div class="row">
                                    <div class="col-5" style="padding-left: 0; padding-right: 5px;">
                                        <select class="form-control" name="hour_close_date" id="hour_close_date" required>
                                            @php $oldHour = (isset($event)) ? date('H', strtotime($event->close_date)) : null @endphp
                                            @for ($i = 0; $i <= 23; $i++)
                                                @php $hour = str_pad($i, 2, '0', STR_PAD_LEFT) @endphp
                                                @if(isset($event))
                                                    <option value="{!! $hour !!}" @if( old('hour_close_date', $oldHour) == $hour) selected @endif>{!! $hour !!}</option>
                                                @else
                                                    <option value="{!! $hour !!}" @if( old('hour_close_date') == $hour) selected @endif>{!! $hour !!}</option>
                                                @endif
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-sm-1 text-center" style="padding-left: 0; padding-right: 0; max-width: 1%;"><b>:</b></div>
                                    <div class="col-5" style="padding-left: 5px; padding-right: 0;">
                                        <select class="form-control" name="min_close_date" id="min_close_date" required>
                                            @php $oldMinute = (isset($event)) ? date('i', strtotime($event->close_date)) : null @endphp
                                            @for($i = 0; $i < 60; $i+=5)
                                                @php $minute = str_pad($i, 2, '0', STR_PAD_LEFT) @endphp
                                                @if(isset($event))
                                                    <option value="{!! $minute !!}" @if( old('min_close_date', $oldMinute) == $minute) selected @endif>{!! $minute !!}</option>
                                                @else
                                                    <option value="{!! $minute !!}" @if( old('min_close_date') == $minute) selected @endif>{!! $minute !!}</option>
                                                @endif
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <select class="form-control" name="close_date_time" id="close_date_time" required>
                                    @for($i = 0; $i < 11; $i++)
                                        <option value="{!! $i !!}" @if( old('close_date_time') == $i) selected @endif>{!! $i !!}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-4">
                                <select class="form-control" name="close_date_mode" id="close_date_mode" required>
                                    <option value="hours" @if( old('close_date_mode') == "hours") selected @endif>{!! trans('event.hours_before') !!}</option>
                                    <option value="days" @if( old('close_date_mode') == "days") selected @endif>{!! trans('event.days_before') !!}</option>
                                    <option value="weeks" @if( old('close_date_mode') == "weeks") selected @endif>{!! trans('event.weeks_before') !!}</option>
                                    <option value="months" @if( old('close_date_mode') == "months") selected @endif>{!! trans('event.months_before') !!}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
        @elseif(!isset($event) && $type_add=='GROUP')
            <div class="row form-group" id="div_open_close_dates">
                <div class="col-md-6">
                    <label class="control-label">{!! trans('event.open_date') !!}</label>
                    <div class="row">
                        <div class="col-md-4">
                            <select class="form-control" name="open_date_select" id="open_date_select" required>
                                <option value="before_starts" @if( old('open_date_select') == "before_starts") selected @endif>{!! trans('event.before_starts') !!}</option>
                                <option value="now" @if( old('open_date_select') == "now") selected @endif>{!! trans('event.immediately') !!}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" name="open_date_time" id="open_date_time" required>
                                @for($i = 0; $i < 11; $i++)
                                    <option value="{!! $i !!}" @if( old('open_date_time') == $i) selected @endif>{!! $i !!}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" name="open_date_mode" id="open_date_mode" required>
                                <option value="hours" @if( old('open_date_mode') == "hours") selected @endif>{!! trans('event.hours_before') !!}</option>
                                <option value="days" @if( old('open_date_mode') == "days") selected @endif>{!! trans('event.days_before') !!}</option>
                                <option value="weeks" @if( old('open_date_mode') == "weeks") selected @endif>{!! trans('event.weeks_before') !!}</option>
                                <option value="months" @if( old('open_date_mode') == "months") selected @endif>{!! trans('event.months_before') !!}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="control-label">{!! trans('event.close_date') !!}</label>
                    <div class="row">
                        <div class="col-md-4">
                            <select class="form-control" name="close_date_select" id="close_date_select" required>
                                <option value="date" @if( old('close_date_select') == "date") selected @endif>{!! trans('event.before_starts') !!}</option>
                                <option value="when_starts" @if( old('close_date_select') == "when_starts") selected @endif>{!! trans('event.when_starts') !!}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" name="close_date_time" id="close_date_time" required>
                                @for($i = 0; $i < 11; $i++)
                                <option value="{!! $i !!}" @if( old('close_date_time') == $i) selected @endif>{!! $i !!}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" name="close_date_mode" id="close_date_mode" required>
                                <option value="hours" @if( old('close_date_mode') == "hours") selected @endif>{!! trans('event.hours_before') !!}</option>
                                <option value="days" @if( old('close_date_mode') == "days") selected @endif>{!! trans('event.days_before') !!}</option>
                                <option value="weeks" @if( old('close_date_mode') == "weeks") selected @endif>{!! trans('event.weeks_before') !!}</option>
                                <option value="months" @if( old('close_date_mode') == "months") selected @endif>{!! trans('event.months_before') !!}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row form-group">
            <div class="col-md-6">
                <label class="control-label">{!! trans('attendance.attendance_answers') !!}</label>
                <select class="selectize2 form-control" placeholder="{!! trans('general.tags') !!}" name="answers[]" style="width: 100%" multiple>
                    @php $oldAnswers = isset($event) ? $event->answersArray('VALUE') : []; @endphp
                    @foreach ($attendance_answers as $answer)
                        <option value="{{ $answer->getValue() }}" @if(in_array($answer->getValue(), old('answers',$oldAnswers))) selected @endif>{{ $answer->getName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="control-label">{!! trans('casteller.address') !!}</label>
                <textarea class="form-control" name="address" id="address" cols="10" rows="5">@if(isset($event)){!! old('address',$event->address) !!}@else{!! old('address') !!}@endif</textarea>
            </div>
            <div class="col-md-6">
                <label class="control-label">{!! trans('event.location_link') !!}</label>
                <textarea class="form-control" name="location_link" id="location_link" cols="1" rows="1">@if(isset($event)){!! old('location_link',$event->location_link) !!}@else{!! old('location_link') !!}@endif</textarea>
            </div>
            <div class="col-md-6">
                <label class="control-label">{!! trans('casteller.additional_information') !!}</label>
                <textarea class="form-control" name="comments" id="comments" cols="10" rows="5">@if(isset($event)){!! old('comments',$event->comments) !!}@else{!! old('comments') !!}@endif</textarea>
            </div>

        </div>

        <div class="row form-group">
            <div class="col-md-6 text-left">
                <button class="btn btn-primary" href=""><i class="fa fa-save"></i> {!! trans('general.save') !!}</button>
                <a href="{{ route('events.list') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> {!! trans('general.back') !!} </a>
            </div>
            <div class="col-md-6 text-right">
                @if(isset($event))
                    <button class="btn btn-danger btn-delete-event" data-id_event="{!! $event->id_event !!}"><i class="fa fa-trash-o"></i> {!! trans('general.delete') !!}</button>
                @endif

            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>

<!-- START - MODAL DELETE -->
<div class="modal fade" id="modalDelEvent" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-popin" role="document">
        <div class="modal-content">
            {!! Form::open(array('url' => 'n', 'method' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'fromDelEvent')) !!}
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">{!! trans('casteller.del_casteller') !!}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>

                <div class="block-content text-center">
                    <i class="fa fa-warning" style="font-size: 46px;"></i>
                    <h3 class="semibold modal-title text-danger">{!! trans('general.caution') !!}</h3>
                    <p class="text-muted">{!! trans('event.del_event_warning') !!}</p>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::submit(trans('general.delete') , array('class' => 'btn btn-danger')) !!}
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
            </div>
            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!--/ END - MODAL DELETE -->

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

            $('.btn-delete-event').on('click', function (e)
            {
                e.preventDefault();

                var id_event = $(this).data().id_event;
                $('#modalDelEvent').modal('show');

                var url = "{{ route('events.destroy', ':id_event') }}";
                url = url.replace(':id_event',id_event);

                $('#fromDelEvent').attr('action', url);
            });

                @if(isset($event) || $type_add=='ONE')

                    $('#div_close_date').show();
                    $('#div_close_time').show();

                    $('#close_date').prop('required', true);
                    $('#hour_close_date').prop('required', true);
                    $('#min_close_date').prop('required', true);

                    $('#close_date_time').hide();
                    $('#close_date_mode').hide();

                    $('#close_date_time').prop('required', false);
                    $('#close_date_mode').prop('required', false);


                    $('#div_open_date').show();
                    $('#div_open_time').show();

                    $('#open_date').prop('required', true);
                    $('#hour_open_date').prop('required', true);
                    $('#min_open_date').prop('required', true);

                    $('#open_date_time').hide();
                    $('#open_date_mode').hide();

                    $('#open_date_time').prop('required', false);
                    $('#open_date_mode').prop('required', false);

                    $("#start_date, #close_date, #open_date").datepicker({
                        @if (Auth()->user()->language=='ca')
                        language: 'ca',
                        @elseif(Auth()->user()->language=='es')
                        language: 'es',
                        @endif
                        format: "dd/mm/yyyy"
                    });

                    $('#close_date_select').on('change', function()
                    {
                        var val = $('#close_date_select').val();

                        if(val==='when_starts')
                        {
                            $('#div_close_date').hide();
                            $('#div_close_time').hide();

                            $('#close_date').prop('required', false);
                            $('#hour_close_date').prop('required', false);
                            $('#min_close_date').prop('required', false);

                            $('#close_date_time').hide();
                            $('#close_date_mode').hide();

                            $('#close_date_time').prop('required', false);
                            $('#close_date_mode').prop('required', false);

                        }
                        else if(val==='before_starts')
                        {
                            $('#div_close_date').hide();
                            $('#div_close_time').hide();

                            $('#close_date').prop('required', false);
                            $('#hour_close_date').prop('required', false);
                            $('#min_close_date').prop('required', false);

                            $('#close_date_time').show();
                            $('#close_date_mode').show();

                            $('#close_date_time').prop('required', true);
                            $('#close_date_mode').prop('required', true);
                        }
                        else
                        {
                            $('#div_close_date').show();
                            $('#div_close_time').show();

                            $('#close_date').prop('required', true);
                            $('#hour_close_date').prop('required', true);
                            $('#min_close_date').prop('required', true);

                            $('#close_date_time').hide();
                            $('#close_date_mode').hide();

                            $('#close_date_time').prop('required', false);
                            $('#close_date_mode').prop('required', false);
                        }
                    });

                    $('#open_date_select').on('change', function()
                    {
                        var val = $('#open_date_select').val();

                        if(val==='now')
                        {
                            $('#div_open_date').hide();
                            $('#div_open_time').hide();

                            $('#open_date').prop('required', false);
                            $('#hour_open_date').prop('required', false);
                            $('#min_open_date').prop('required', false);

                            $('#open_date_time').hide();
                            $('#open_date_mode').hide();

                            $('#open_date_time').prop('required', false);
                            $('#open_date_mode').prop('required', false);
                        }
                        else if(val==='before_starts')
                        {
                            $('#open_date_time').show();
                            $('#open_date_mode').show();

                            $('#open_date_time').prop('required', true);
                            $('#open_date_mode').prop('required', true);

                            $('#div_open_date').hide();
                            $('#div_open_time').hide();

                            $('#open_date').prop('required', false);
                            $('#hour_open_date').prop('required', false);
                            $('#min_open_date').prop('required', false);

                        }
                        else
                        {
                            $('#div_open_date').show();
                            $('#div_open_time').show();

                            $('#open_date').prop('required', true);
                            $('#hour_open_date').prop('required', true);
                            $('#min_open_date').prop('required', true);

                            $('#open_date_time').hide();
                            $('#open_date_mode').hide();

                            $('#open_date_time').prop('required', false);
                            $('#open_date_mode').prop('required', false);
                        }
                    });

                    function checkVisibility(){

                        let visibility = document.querySelector('.visibility.visible-lg .js-switchery');

                        if(!visibility.checked)
                        {
                            $('#div_open_close_dates').hide();

                            $('#open_date').prop('required', false);
                            $('#hour_open_date').prop('required', false);
                            $('#min_open_date').prop('required', false);
                            $('#close_date').prop('required', false);
                            $('#hour_close_date').prop('required', false);
                            $('#min_close_date').prop('required', false);
                        }
                        else
                        {
                            $('#div_open_close_dates').show();

                            $('#open_date').prop('required', true);
                            $('#hour_open_date').prop('required', true);
                            $('#min_open_date').prop('required', true);
                            $('#close_date').prop('required', true);
                            $('#hour_close_date').prop('required', true);
                            $('#min_close_date').prop('required', true);
                        }
                    }

                @elseif(!isset($event) && $type_add=='GROUP')

                    $("#datepicker_start_date").datepicker(
                        {
                        @if (Auth()->user()->language=='ca')
                        language: 'ca',
                        @elseif(Auth()->user()->language=='es')
                        language: 'es',
                        @endif
                        todayHighlight: true,
                        multidate: true,
                        format: "dd/mm/yyyy"
                    });

                    //var dates = [];
                    $("#datepicker_start_date").on('changeDate', function(event)
                    {
                        /*var year = String(event.date).split(" ")[3];
                        var day = String(event.date).split(" ")[2];
                        var month = event.date.getMonth() + 1;
                        month = month.toString().padStart(2, '0');

                        var date = day+'/'+month+'/'+year;
                        dates.push(date);

                        $("input[name='start']").val(dates);*/

                        $("input[name='start_dates']").val($('#datepicker_start_date').datepicker('getDates'));

                        $('.num-events').html($('#datepicker_start_date').datepicker('getDates').length);
                    });


                    $('#open_date_select').on('change', function ()
                    {
                        var val = $('#open_date_select').val();

                        if(val==='now')
                        {
                            $('#open_date_time').hide();
                            $('#open_date_mode').hide();

                            $('#open_date_time').prop('required', false);
                            $('#open_date_mode').prop('required', false);
                        }
                        else
                        {
                            $('#open_date_time').show();
                            $('#open_date_mode').show();

                            $('#open_date_time').prop('required', true);
                            $('#open_date_mode').prop('required', true);
                        }
                    });

                    $('#close_date_select').on('change', function ()
                    {
                        var val = $('#close_date_select').val();

                        if(val==='when_starts')
                        {
                            $('#close_date_time').hide();
                            $('#close_date_mode').hide();

                            $('#close_date_time').prop('required', false);
                            $('#close_date_mode').prop('required', false);
                        }
                        else
                        {
                            $('#close_date_time').show();
                            $('#close_date_mode').show();

                            $('#close_date_time').prop('required', true);
                            $('#close_date_mode').prop('required', true);
                        }
                    });


            function checkVisibility(){

                let visibility = document.querySelector('.visibility.visible-lg .js-switchery');

                if(!visibility.checked)
                {
                    $('#div_open_close_dates').hide();

                    $('#open_date_time').prop('required', false);
                    $('#open_date_mode').prop('required', false);
                    $('#close_date_time').prop('required', false);
                    $('#close_date_mode').prop('required', false);
                }
                else
                {
                    $('#div_open_close_dates').show();

                    $('#open_date_time').prop('required', true);
                    $('#open_date_mode').prop('required', true);
                    $('#close_date_time').prop('required', true);
                    $('#close_date_mode').prop('required', true);
                }
            }


                @endif

                $('.visibility.visible-lg').on('change', '.js-switchery', function ()
                {
                    checkVisibility();

                });

                checkVisibility();

                $('#close_date_select').change();
                $('#open_date_select').change();




        });

    </script>
@endsection
