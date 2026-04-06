@extends('members.template.main')

@section('title', $event->getName() . ' - ' . trans('general.form'))

@section('css_before')
    <link rel="stylesheet" href="{{ asset('css/members/event-form.css') }}">
@endsection

@section('content')

<div class="event-form-wrapper">
    <div class="event-form-card">

        {{-- Header --}}
        <div class="event-form-header">
            <span class="event-type-badge">{{ $event->getTypeName() }}</span>
            <h1>{!! $event->getName() !!}</h1>
        </div>

        {{-- Meta info --}}
        <div class="event-meta">
            <div class="meta-item">
                <i class="bi bi-calendar2-check"></i>
                {!! App\Helpers\Humans::parseDate($event->getStartDate()) !!}
            </div>
            <div class="meta-item">
                <i class="bi bi-clock-history"></i>
                {{ round($event->getDuration()/60,1) }} {!! trans('event.hours') !!}
            </div>
            @if($event->getAddress())
                <div class="meta-item">
                    <i class="bi bi-geo-alt"></i>
                    @if($event->getLocationLink())
                        <a href="{{ $event->getLocationLink() }}" target="_blank">{{ $event->getAddress() }}</a>
                    @else
                        {{ $event->getAddress() }}
                    @endif
                </div>
            @endif
        </div>

        {{-- Description --}}
        @if($event->getComments())
        <div class="event-description">
            <pre>{{ $event->getComments() }}</pre>
        </div>
        @endif

        {{-- Status banner --}}
        @if(!$event->hasAttendanceAnswers())
            <div class="event-status-banner no-form">
                <i class="fa fa-exclamation-triangle"></i>
                {!! trans('attendance.no_form_configured') !!}
            </div>
        @elseif(!$event->isOpen())
            <div class="event-status-banner closed">
                <i class="fa fa-lock"></i>
                {!! trans('attendance.event_closed') !!}
            </div>
        @else
            <div class="event-status-banner open">
                <i class="fa fa-unlock"></i>
                {!! trans('attendance.event_open') !!}
            </div>
        @endif

        {{-- Success message --}}
        <div class="form-success-msg" id="form-success-msg">
            <i class="fa fa-check-circle"></i>
            <span>{!! trans('attendance.form_saved_success') !!}</span>
        </div>

        {{-- Form body --}}
        @if($event->hasAttendanceAnswers())
        <div class="event-form-body">
            <h3><i class="fa fa-list-alt"></i> {!! trans('general.form') !!}</h3>
            <form id="fb-rendered-form">
                <div id="fb-reader"></div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="event-form-footer">
            @if($event->isOpen())
                <button type="button" class="btn-save-form" id="submit-form-btn">
                    <i class="fa fa-save"></i> {!! trans('general.save') !!}
                </button>
            @endif
            <a href="{{ route('member.calendar') }}" class="btn-back-calendar">
                <i class="fa fa-arrow-left"></i> {!! trans('general.back') !!}
            </a>
        </div>
        @else
        <div class="event-form-footer">
            <a href="{{ route('member.calendar') }}" class="btn-back-calendar">
                <i class="fa fa-arrow-left"></i> {!! trans('general.back') !!}
            </a>
        </div>
        @endif

    </div>
</div>

@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://formbuilder.online/assets/js/form-render.min.js"></script>

    <script>
        $(function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            var formData = {!! $schema !!} || [];
            var userOptions = {!! $userOptions !!} || {};

            if (formData && formData.length > 0) {
                var formRenderInstance = $('#fb-reader').formRender({
                    formData: formData,
                    notify: {
                        error: function(message) { return console.error(message); },
                        success: function(message) { return console.log(message); },
                        warning: function(message) { return console.warn(message); }
                    }
                });

                // Pre-fill user options
                if (Object.keys(userOptions).length > 0) {
                    setTimeout(function() {
                        $.each(userOptions, function(key, val) {
                            var field = $('[name="'+key+'"], [name="'+key+'[]"]');
                            if(field.length > 0) {
                                if(field.is(':checkbox') || field.is(':radio')){
                                    if(Array.isArray(val)) {
                                        field.each(function(){
                                            if(val.includes($(this).val())) {
                                                $(this).prop('checked', true);
                                            }
                                        });
                                    } else {
                                        field.filter('[value="'+val+'"]').prop('checked', true);
                                    }
                                } else {
                                    field.val(val);
                                }
                            }
                        });
                    }, 150);
                }
            }

            // Submit handler
            $(document).on('click', '#submit-form-btn', function(e) {
                e.preventDefault();
                var btn = $(this);
                var originalText = btn.html();
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ trans("general.saving") }}...');

                try {
                    var formArray = $("#fb-rendered-form").serializeArray();
                    var answers = {};
                    for (var i = 0; i < formArray.length; i++){
                        var param = formArray[i];
                        var name = param.name;
                        if (name.endsWith('[]')) {
                            name = name.slice(0, -2);
                        }
                        if (answers[name] === undefined) {
                            answers[name] = param.value;
                        } else {
                            if (!Array.isArray(answers[name])) {
                                answers[name] = [answers[name]];
                            }
                            answers[name].push(param.value);
                        }
                    }

                    $.post("{{ route('member.edit.event-set-answers') }}", {
                        'id_event': {{ $event->getId() }},
                        'answers': answers
                    }).done(function (data) {
                        btn.prop('disabled', false).html(originalText);
                        $('#form-success-msg').addClass('visible');
                        setTimeout(function() {
                            $('#form-success-msg').removeClass('visible');
                        }, 4000);
                    }).fail(function(xhr) {
                        var msj = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error';
                        alert(msj);
                        btn.prop('disabled', false).html(originalText);
                    });
                } catch(error) {
                    console.error(error);
                    alert("Error: " + error.message);
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    </script>
@endsection
