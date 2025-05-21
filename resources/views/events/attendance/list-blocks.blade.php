@extends('template.main')

@section('title', trans('general.attendance'))
@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
    <style type="text/css">
        .companions {
            width: 65px !important;
        }
    </style>
@endsection

@section('content')

<div class="block">

    <div class="block-header block-header-default">

        <div class="block-title">
            <h3 class="block-title"><b>{!! trans('attendance.attendance') !!}:</b> {!! $event->name !!} <span class="text-muted">({!! \App\Helpers\Humans::readEventColumn($event, 'start_date'); !!})</span></h3>
        </div>
        <div class="block-options">
            <select name="view_list" id="viewList" class="selectize2-no-search form-control">
                <option value="COLUMNS" selected>{!! trans('event.view_list_columns') !!}</option>
                <option value="LIST">{!! trans('event.view_list_list') !!}</option>
            </select>
        </div>

    </div>

    <div class="block-content block-content-full">
        <div class="row text-right">
            <div class="offset-md-8 col-md-4">
                <div class="row text-center">
                    <div class="col-2 text-success h4"><i class="{!! \App\Helpers\RenderHelper::getAttendanceIcon(\App\Enums\ScaledAttendanceStatus::YESVERIFIED); !!}"></i></div>
                    <div class="col-2 text-success h4"><i class="{!! \App\Helpers\RenderHelper::getAttendanceIcon(\App\Enums\ScaledAttendanceStatus::YES); !!}"></i></div>
                    <div class="col-2 text-danger h4"><i class="{!! \App\Helpers\RenderHelper::getAttendanceIcon(\App\Enums\ScaledAttendanceStatus::NO); !!}"></i></div>
                    <div class="col-2 text-warning h4"><i class="{!! \App\Helpers\RenderHelper::getAttendanceIcon(\App\Enums\ScaledAttendanceStatus::UNKNOWN); !!}"></i></div>
                    <div class="col-2 text-secondary h4"><i class="fa-solid fa-users"></i></div>
                    <div class="w-100"></div>
                    <div class="col-2 text-success h4" id="VERIFIED_YES">{!! $event->countAttenders()['verified_ok'] !!}</div>
                    <div class="col-2 text-success h4" id="YES">{!! $event->countAttenders()['ok'] !!}</div>
                    <div class="col-2 text-danger h4" id="NO">{!! $event->countAttenders()['nok'] !!}</div>
                    <div class="col-2 text-warning h4" id="UNKNOWN">{!! $event->countAttenders()['unknown'] !!}</div>
                    <div class="col-2 text-secondary h4">{!! $event->countAttenders()['companions'] !!}</div>
                </div>
            </div>
        </div>

        @foreach($positions as $position)
            <?php $i = 1; ?>

                @if(!empty($castellers[$position->getValue()] ))

                    <h3><span class="pr-10">{!! $position->getName() !!}</span>
                        <span class="text-success h5 pr-5">
                            <i class="{!! \App\Helpers\RenderHelper::getAttendanceIcon(\App\Enums\ScaledAttendanceStatus::YESVERIFIED); !!} pr-5"></i>{!! $attendenceStatusVerifiedOK[$position->getValue()]['statusVerifiedOK'] !!}
                        </span>
                        <span class="text-success h5 pr-5">
                            <i class="{!! \App\Helpers\RenderHelper::getAttendanceIcon(\App\Enums\ScaledAttendanceStatus::YES); !!} pr-5"></i>{!! $attendanceStatusOK[$position->getValue()]['statusOK'] !!}
                        </span>
                        <span class="text-muted h5">
                            <i class="fa-solid fa-users pr-5"></i>{!! count($castellers[$position->getValue()]) !!}
                        </span>
                    </h3>
                    <div class="row" style="padding-bottom: 25px;">

                    @foreach($castellers[$position->getValue()] as $casteller)
                        @if(($i==1))
                         <div class="col-md-3">
                             <table class="table table-hover table-bordered table-striped table-sm">
                                 <thead>
                                <tr>
                                    <th style="width:80%">{!! trans('general.name') !!}</th>
                                    <th style="width:22px text-align: right"><i class="fa-solid fa-check" style="font-size: 22px;"></i></th>
                                    <th style="width:22px text-align: right"><i class="fa-solid fa-check-double" style="font-size: 22px;"></i></th>

                                </tr>
                                </thead>
                                        <tbody>
                            @endif

                            <tr>
                                <td style="width:80%">{!! $casteller['displayName'] !!}</td>
                                <td style="width:22px text-align: right">{!! $casteller['status'] !!}</td>
                                <td style="width:22px text-align: right">{!! $casteller['status_verified'] !!}</td>
                            </tr>

                            @if(($i == round(count($castellers[$position->getValue()]) / 4)) || ($i == round(count($castellers[$position->getValue()]) / 4)*2) || ($i == round(count($castellers[$position->getValue()]) / 4)*3))
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3">
                                    <table class="table table-hover table-bordered table-striped table-sm">
                                        <thead>
                                        <tr>
                                            <th style="width:80%">{!! trans('general.name') !!}</th>
                                            <th style="width:22px text-align: right"><i class="fa-solid fa-check" style="font-size: 22px;"></i></th>
                                            <th style="width:22px text-align: right"><i class="fa-solid fa-check-double" style="font-size: 22px;"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                            @endif
                            <?php $i++; ?>
                    @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>

                @endif


        @endforeach

    </div>
</div>

@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    @if (Auth()->user()->language=='ca')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.ca.min.js') !!}"></script>
    @elseif(Auth()->user()->language=='es')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js') !!}"></script>
    @endif

    <!-- Page JS Code -->
    <script type="text/javascript">
        $(function () {
            $.ajaxPrefilter(function(options, originalOptions, xhr) { // this will run before each request
                var token = $('meta[name="csrf-token"]').attr('content'); // or _token, whichever you are using

                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token); // adds directly to the XmlHttpRequest Object
                }
            });
        });
    </script>
    <script>
        $(function ()
        {
            $(".selectize2-no-search").select2({
                minimumResultsForSearch: -1,
                language: "ca"
            });

            @can('edit events')

                function setStatus(id_casteller, status, companions, answers)
                {
                    $.post( "{{ route('event.attendance.set-status') }}",
                        { 'id_casteller': id_casteller,
                            'id_event' : {!! $event->id_event !!},
                            'status': status });
                }

                function setStatusVerified(id_casteller, status)
                {
                    $.post( "{{ route('event.attendance.set-status-verified') }}",
                        { 'id_casteller': id_casteller,
                            'id_event' : {!! $event->id_event !!},
                            'status': status });
                }

                $('.btn-status').on('click', function ()
                {
                    var status;
                    var id_casteller = $(this).data().id_casteller;

                    var YES = parseInt($('#YES').html());
                    var NO = parseInt($('#NO').html());
                    var UNKNOWN = parseInt($('#UNKNOWN').html());

                    if($(this).hasClass('btn-success'))
                    {
                        status = 2;
                        $(this).removeClass( "btn-success" );
                        $(this).addClass( "btn-danger" );
                        $(this).html('<i class="fa-solid fa-close"></i>');
                        YES--;
                        NO++;
                        $('#YES').html(YES);
                        $('#NO').html(NO);
                    }
                    else if($(this).hasClass('btn-danger'))
                    {
                        status = 3;
                        $(this).removeClass( "btn-danger" );
                        $(this).addClass( "btn-outline-warning" );
                        $(this).html('<i class="fa-solid fa-question"></i>');
                        NO--;
                        UNKNOWN++;
                        $('#UNKNOWN').html(UNKNOWN);
                        $('#NO').html(NO);
                    }
                    else if($(this).hasClass('btn-secondary') || $(this).hasClass('btn-outline-warning'))
                    {
                        status = 1;
                        $(this).removeClass( "btn-secondary" );
                        $(this).removeClass('btn-outline-warning')
                        $(this).addClass( "btn-success" );
                        $(this).html('<i class="fa-solid fa-check"></i>');
                        YES++;
                        UNKNOWN--;
                        $('#UNKNOWN').html(UNKNOWN);
                        $('#YES').html(YES);
                    }

                    setStatus(id_casteller, status);
                });

                $('.btn-status-verified').on('click', function ()
                {
                    var status;
                    var id_casteller = $(this).data().id_casteller;

                    let VERIFIED_YES = parseInt($('#VERIFIED_YES').html());

                    if($(this).hasClass('btn-success'))
                    {
                        status = 2;
                        $(this).removeClass( "btn-success" );
                        $(this).addClass( "btn-danger" );
                        $(this).html('<i class="fa-solid fa-close"></i>');
                        VERIFIED_YES--;
                        $('#VERIFIED_YES').html(VERIFIED_YES);
                    }
                    else if($(this).hasClass('btn-danger'))
                    {
                        status = 3;
                        $(this).removeClass( "btn-danger" );
                        $(this).addClass( "btn-secondary" );
                        $(this).html('<i class="fa-solid fa-question"></i>');
                    }
                    else if($(this).hasClass('btn-secondary'))
                    {
                        status = 1;
                        $(this).removeClass( "btn-secondary" );
                        $(this).addClass( "btn-success" );
                        $(this).html('<i class="fa-solid fa-check"></i>');
                        VERIFIED_YES++;
                        $('#VERIFIED_YES').html(VERIFIED_YES);
                    }

                    setStatusVerified(id_casteller, status);
                });


            @endcan

            $('#viewList').on('change', function(e)
            {
                var value = $(this).val();

                if(value==='LIST')
                {
                    window.location.href = '{{ route('event.attendance', $event->id_event) }}';
                }
                else if(value==='COLUMNS')
                {
                    window.location.href = '{{ route('event.attendance.list-block', $event->id_event) }}';
                }
            });
        });
    </script>
@endsection
