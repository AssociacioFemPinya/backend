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
        #totalCastellers,#attendance-summary .row{
            font-size: 1.5rem;
        }
        #filter-options-icons{
            cursor:pointer;
        }
    </style>
@endsection

@section('content')

    <div class="block">

        <div class="block-header block-header-default row">
            <div class="block-title col-12 col-sm-8 col-md-9 col-xl-10">
                <h3 class="block-title"><b>{!! trans('attendance.attendance') !!}:</b> {!! $event->getName() !!} <span class="text-muted">({!! \App\Helpers\Humans::readEventColumn($event, 'start_date'); !!})</span></h3>
            </div>
            @if(Auth::user()->getColla()->getConfig()->getBoardsEnabled())
                <div class="block-options col-12 col-sm-4 col-md-3 col-xl-2">
                    <select name="view_list" id="viewList" class="selectize2-no-search form-control">
                        <option value="LIST" selected>{!! trans('event.view_list_list') !!}</option>
                        <option value="COLUMNS">{!! trans('event.view_list_columns') !!}</option>
                    </select>
                <!--a class="btn btn-outline-primary" href="{!! route('event.attendance.list-block', $event->getId()) !!}"><i class="fa fa-columns"></i></a-->
                </div>
            @endif

        </div>

        <div class="block-content block-content-full">
            <div id="filter-header" class="row">
                <div class="col-sm-3 col-xl-1 text-center pb-10">
                    <label class="control-label">
                        {!! trans('general.filter') !!}
                    </label>
                    <select name="filter_search_type" id="filter_search_type" class="selectize2-no-search form-control">
                        <option value="{{ \App\Enums\FilterSearchTypesEnum::AND }}">AND</option>
                        <option value="{{ \App\Enums\FilterSearchTypesEnum::OR }}" selected>OR</option>
                    </select>
                </div>
                <div class="col-sm-9 col-xl-3 text-center pb-10">
                    <label class="control-label">
                        {!! trans('general.tags') !!}
                    </label>
                    <select name="tags[]" id="tags" class="selectize2 form-control" style="width: 100%;" multiple>
                        <option value="all" selected>{!! trans('casteller.everybody') !!}</option>
                        @foreach($tags as $tag)
                            <option value="{!! $tag->getId() !!}">{!! $tag->getName() !!}</option>
                        @endforeach
                        @if(Auth::user()->getColla()->getConfig()->getBoardsEnabled())
                            <option value="" disabled>{!! trans('casteller.positions') !!}</option>
                            @foreach($positions as $position)
                                <option value="{!! $position->getId() !!}">{!! $position->getName() !!}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-xl-1 text-center pb-10">
                    <label class="control-label">
                        <i class="fa-solid fa-check"></i>
                    </label>
                    <select name="status[]" id="status" class="selectize2 form-control" multiple>
                        <option value="{{ \App\Enums\AttendanceStatus::YES }}">{!! trans('general.yes') !!}</option>
                        <option value="{{ \App\Enums\AttendanceStatus::NO }}">{!! trans('general.no') !!}</option>
                        <option value="{{ \App\Enums\AttendanceStatus::UNKNOWN }}">{!! trans('general.unknown') !!}</option>
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-xl-1 text-center pb-10">
                    <label class="control-label">
                        <i class="fa-solid fa-check-double"></i>
                    </label>
                    <select name="statusVerified[]" id="statusVerified" class="selectize2 form-control" multiple>
                        <option value="{{ \App\Enums\AttendanceStatus::YES }}">{!! trans('general.yes') !!}</option>
                        <option value="{{ \App\Enums\AttendanceStatus::NO }}">{!! trans('general.no') !!}</option>
                        <option value="{{ \App\Enums\AttendanceStatus::UNKNOWN }}">{!! trans('general.unknown') !!}</option>
                    </select>
                </div>
                <div id="filter-icons" class="col-6 col-sm-3 col-xl-1 text-center pb-10">
                    <label class="control-label">
                        {!! trans('general.info') !!}
                    </label>
                    <div>
                        <span id="totalCastellers" class="pr-10"></span> <i class="fa-solid fa-2x fa-users"></i>
                    </div>
                </div>
                <div id="filter-options-icons" class="col-6 col-sm-3 col-xl-2 text-center pb-10">
                    <label class="control-label">
                        {!! trans('general.options') !!}
                    </label>
                    <div>
                        <i id="resetFilter" class="fa-solid fa-2x fa-arrow-rotate-right pr-15 text-primary" data-toggle="tooltip" data-placement="right" title="{!! trans('general.tooltip_reset_filter') !!}"></i>
                        <a href="{{ route('event.attendance.list-attenders-csv', $event->getId()) }}"><i id="exportFilter" class="fa-solid fa-2x fa-cloud-arrow-down text-primary pr-15 " data-toggle="tooltip" data-placement="right" title="{!! trans('general.tooltip_export_attendance') !!}"></i></a>
                        <i id="sendReminder" class="fa-solid fa-2x fa-bell text-primary" data-toggle="tooltip" data-placement="right" title="{!! trans('notifications.tooltip_send_reminder') !!}"></i>

                    </div>
                </div>
                <div id="attendance-summary" class="col-xl-3 text-center">
                    <label class="control-label">
                        {!! trans('attendance.attendance') !!}
                    </label>
                    <div class="row">
                        <div class="col text-success"><i class="fa-solid fa-check-double"></i></div>
                        <div class="col text-success"><i class="fa-solid fa-check"></i></div>
                        <div class="col text-danger"><i class="fa-solid fa-close"></i></div>
                        <div class="col text-warning"><i class="fa-solid fa-question"></i></div>
                        <div class="col text-secondary"><i class="si si-users"></i></div>
                        <div class="w-100"></div>
                        <div class="col text-success" id="VERIFIED_YES">{!! $event->countAttenders()['verified_ok'] !!}</div>
                        <div class="col text-success" id="YES">{!! $event->countAttenders()['ok'] !!}</div>
                        <div class="col text-danger" id="NO">{!! $event->countAttenders()['nok'] !!}</div>
                        <div class="col text-warning" id="UNKNOWN">{!! $event->countAttenders()['unknown'] !!}</div>
                        <div class="col text-secondary" id="companions">{!! $event->countAttenders()['companions'] !!}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="block">
        <div class="block-content block-content-full">
            <!-- Example single danger button -->
            <div class="row" style="padding-top: 25px;">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" style="width: 100%;" id="attenders">
                            <thead>
                            <tr>
                                <th>{!! trans('general.name') !!}</th>
                                <th><i class="fa-solid fa-check" style="font-size: 22px;"></i></th>
                                <th><i class="fa-solid fa-check-double" style="font-size: 22px;"></i></th>
                                <th>{!! trans('attendance.attendance_answers') !!}</th>
                                <th>{!! trans('attendance.companions') !!}</th>
                                <th>{!! trans('general.last_update') !!}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
            <div class="col-md-6 text-left">
                <a href="{{ route('events.list') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> {!! trans('general.back') !!} </a>
            </div>
        </div>
    </div>

    <!-- START - Modal long -->
    <div class="modal fade" id="modalSendReminder" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popin" role="document">
            <div class="modal-content" id="modelSendReminderContent">
                <!-- MODAL CONTENT -->
                @include('notifications.modals.modal-send-reminder')
            </div>
        </div>
    </div>
    <!-- END - Modal long -->
    @include('modals.modal-success')
    @include('modals.modal-error')

@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    @if (Auth()->user()->getLanguage() === 'ca')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.ca.min.js') !!}"></script>
    @elseif(Auth()->user()->getLanguage() === 'es')
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
            let attenders;
            let personalize_answers = {!! count($event->getAttendanceAnswers()) !!};
            let companions_allowed = {!! ($event->getCompanions()) ? 'true' : 'false'; !!};
            let event_id = {{ $event->getId() }};
            let attendadance_tags ='attendance_tags'+event_id;
            let attendance_status ='attendance_status'+event_id;
            let attendance_status_verified ='attendance_status_verified'+event_id;
            let attendance_filter_search_type ='attendance_filter_search_type'+event_id;


            function drawAttendersTable()
            {
                attenders = $("#attenders").DataTable({
                    "language": {!! trans('datatables.translation') !!},
                    "stateSave": true,
                    "stateDuration": -1,
                    "processing": true,
                    "serverSide": true,
                    "pageLength": 50,
                    "ajax": {
                        "url": "{{ route('event.attendance.list-attenders', $event->getId()) }}",
                        "type": "POST",
                        "data": function ( d ) {
                            return $.extend( {}, d, {
                                "tags": $('#tags').val(),
                                "filter_search_type": $('#filter_search_type').val(),
                                "status": $('#status').val(),
                                "statusVerified": $('#statusVerified').val(),
                            } );
                        }
                    },
                    "ordering":'true',
                    "order": [0, 'asc'],
                    "columns": [
                        { "data": "alias", "name": "alias"},
                        { "data": "status", "name": "status", "orderable": false},
                        { "data": "status_verified", "name": "status_verified", "orderable": false},
                        { "data": "attendance_answers", "name": "attendance_answers", "orderable": false},
                        { "data": "companions", "name": "companions", "orderable": false},
                        { "data": "last_update", "name": "last_update", "orderable": false}
                    ],
                    "columnDefs": [
                        { "width": "35%", "targets": 0 },
                        { "width": "7%", "targets": [1, 2] },
                        { "width": "8%", "targets": 4 },
                        { "width": "20%", "targets": 3 },
                        { "width": "5%", "targets": 5 }
                    ],
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            exportOptions: {columns: [0, 1, 2, 3, 4, 5]}
                        },
                        {
                            extend: 'excelHtml5',
                            exportOptions: {columns: [0, 1, 2, 3, 4, 5]}
                        }
                    ],
                    "drawCallback": function( settings ) {
                        let api = this.api();

                        $('#totalCastellers').html(api.page.info().recordsTotal);
                    }
                });
            }

            function initFilters(){

                if (sessionStorage.getItem(attendadance_tags)) {
                    var tags = JSON.parse(sessionStorage.getItem(attendadance_tags));
                    $('#tags').val(tags).trigger('change');
                }
                if (sessionStorage.getItem(attendance_status)) {
                    var status = JSON.parse(sessionStorage.getItem(attendance_status));
                    $('#status').val(status).trigger('change');
                }
                if (sessionStorage.getItem(attendance_status_verified)) {
                    var statusVerified = JSON.parse(sessionStorage.getItem(attendance_status_verified));
                    $('#statusVerified').val(statusVerified).trigger('change');
                }
                if (sessionStorage.getItem(attendance_filter_search_type)) {
                    $('#filter_search_type').val(sessionStorage.getItem(attendance_filter_search_type)).trigger('change');
                }
            }

            function resetFilters(){
                $("#attenders").DataTable().search('');
                sessionStorage.removeItem(attendadance_tags);
                sessionStorage.removeItem(attendance_status);
                sessionStorage.removeItem(attendance_status_verified);
                sessionStorage.removeItem(attendance_filter_search_type);
                $('#tags').val(["all"]).trigger('change');
                $('#status').val(null).trigger('change');
                $('#statusVerified').val(null).trigger('change');
                $('#filter_search_type').val('OR').trigger('change');
            }

            function sendReminder(){

                var message = document.getElementById("message");
                var messageValue = message.value;

                let url = "{{ route('event.attendance.notify_missing', ['event' => ':event']) }}";
                url = url.replace(':event', event_id);

                $.post(url,
                        {
                            customMessage: messageValue,
                        }
                    )
                    .then(function(result, status){
                        $('#modalSuccess').modal('show');
                    })
                    .fail(function(result, status){
                        $('#modalError').modal('show');
                    });

            }

            initFilters();
            drawAttendersTable();

            attenders.on( 'draw', function () {
                $('.selectize2').select2({language: "ca"});
            });

            $('#tags, #filter_search_type, #status, #statusVerified').change(function(){

                attenders.page(0);
                attenders.state.save();
                var selectedTags = $('#tags').val();
                sessionStorage.setItem(attendadance_tags, JSON.stringify(selectedTags));
                var seletedStatus = $('#status').val();
                sessionStorage.setItem(attendance_status, JSON.stringify(seletedStatus));
                var seletedStatusVerified = $('#statusVerified').val();
                sessionStorage.setItem(attendance_status_verified, JSON.stringify(seletedStatusVerified));
                sessionStorage.setItem(attendance_filter_search_type, $('#filter_search_type').val());
                $('#attenders').DataTable().destroy();
                drawAttendersTable();
            });

            $(".selectize2-no-search").select2({
                minimumResultsForSearch: -1,
                language: "ca"
            });

            @can('edit events')

            $('#attenders').on('click', '.btn-status', function ()
            {
                const $this = $(this);
                const id_casteller = $(this).data().id_casteller;
                let status;

                let YES = parseInt($('#YES').html());
                let NO = parseInt($('#NO').html());
                let UNKNOWN = parseInt($('#UNKNOWN').html());

                if($this.hasClass('btn-success')) {
                    status = 2;
                    YES--;
                    NO++;
                } else if($this.hasClass('btn-danger')) {
                    status = 3;
                    NO--;
                    UNKNOWN++;
                } else if($this.hasClass('btn-secondary') || $this.hasClass('btn-outline-warning')) {
                    status = 1;
                    if($(this).hasClass('btn-outline-warning')) {
                        UNKNOWN--;
                    }
                    YES++;
                }

                $.post( "{{ route('event.attendance.set-status') }}",
                    {
                        'id_casteller': id_casteller,
                        'id_event': {!! $event->getId() !!},
                        'status': status
                    }).done(function(data) {
                        $('#YES').html(YES);
                        $('#NO').html(NO);
                        $('#UNKNOWN').html(UNKNOWN);
                        $("#attenders").DataTable().ajax.reload(null, false );
                });
            });

            $('#attenders').on('click', '.btn-status-verified', function (e)
            {
                e.preventDefault();
                const $this = $(this);
                const id_casteller = $(this).data().id_casteller;
                let status;

                let VERIFIED_YES = parseInt($('#VERIFIED_YES').html());

                if($this.hasClass('btn-success')) {
                    status = 2;
                    VERIFIED_YES--;

                } else if($this.hasClass('btn-danger')) {
                    status = 3;
                } else if($this.hasClass('btn-secondary')) {
                    status = 1;
                    VERIFIED_YES++;

                }

                $.post( "{{ route('event.attendance.set-status-verified') }}",
                    {
                        'id_casteller': id_casteller,
                        'id_event' : {!! $event->getId() !!},
                        'status': status
                    }).done(function(data) {
                        $('#VERIFIED_YES').html(VERIFIED_YES);
                        $("#attenders").DataTable().ajax.reload(null, false );
                });
            });

            $('#attenders').on('change', '.answers', function ()
            {
                var id_casteller = $(this).parents('tr').find('.btn-status').data().id_casteller;
                var answers = $(this).val();

                $.post( "{{ route('event.attendance.set-answers') }}",
                    {
                        'id_casteller': id_casteller,
                        'id_event' : {!! $event->getId() !!},
                        'answers': answers
                    });
            });

            $('#attenders').on('change', '.companions', function ()
            {
                let id_casteller = $(this).parents('tr').find('.btn-status').data().id_casteller;
                let companions = $(this).val();

                if(companions_allowed) {
                    $.post("{{ route('event.attendance.set-companions') }}",
                        {
                            'id_casteller': id_casteller,
                            'id_event': {!! $event->getId() !!},
                            'companions': companions
                        });
                }
            });

            @endcan

            $('#viewList').on('change', function(e)
            {
                let value = $(this).val();

                if(value === 'LIST') {

                    window.location.href = '{{ route('event.attendance', $event->getId()) }}';
                } else if(value === 'COLUMNS') {

                    window.location.href = '{{ route('event.attendance.list-block', $event->getId()) }}';
                }
            });

            $('#resetFilter').on('click', function (event)
            {
                resetFilters();
            });


            $('#sendReminder').on('click', function (event)
            {
                $(this).tooltip('hide');
                $('#modalSendReminder').modal('show');
            });

            $(".btn-send-reminder").on('click', function(){
                sendReminder();
            });

        });
    </script>
@endsection
