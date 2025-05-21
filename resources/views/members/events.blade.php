@extends('members.template.main')

@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
    <link href="{{ asset('css/modals/event-info.css') }}" rel="stylesheet">
@endsection

@section('content')

<div id="upcoming_events">
    <div class="block">
        <div class="block-header ">

            <div class="block-title">
                <h3 class="block-title">{!! $event_type_name !!}</h3>
            </div>
        </div>
        <div class="block-content">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped table-vcenter" style="width: 100%;" id="events_upcoming">
                    <thead>
                    <tr>
                        <th></th>
                        <th>{!! trans('general.name') !!}</th>
                        <th>{!! trans('general.date') !!}</th>
                        <th>{!! trans('tag.add_attendance_tag') !!}</th>
                        <th>{!! trans('attendance.companions') !!}</th>
                        <th>#</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- START - Modal long -->
<div class="modal fade" id="modalEventInfo" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-popin" role="document" style="display:table;">
        <div class="modal-content flex flex-column w-xxs mx-auto rounded overflow-hidden bs-1 mb-3" id="modalEventInfoContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>

<!-- END - Modal long -->

@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <script src="{!! asset('js/plugins/magnific-popup/jquery.magnific-popup.js') !!}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/buttons.html5.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.js') }}"></script>


    <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    @if (Auth()->user()->getLanguage() === 'ca')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.ca.min.js') !!}"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/ca.min.js"></script>

    @elseif(Auth()->user()->getLanguage() === 'es')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js') !!}"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/es.min.js"></script>

    @endif
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
            // ATTENDANCE TAB

            function drawAttendanceTable()
            {
                var events_upcoming = $("#events_upcoming").DataTable({
                    "language": {!! trans('datatables.translation') !!},
                    "stateSave": true,
                    "stateDuration": -1,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{ route('member.get.event-attendance') }}",
                        "type": "POST",
                        @if(isset($event_type))
                        "data": function ( d ) {
                            return $.extend( {}, d, {
                                "event_type": {{ $event_type }},
                            } );
                        }
                        @endif
                    },
                    "createdRow": function( row, data, dataIndex ) {
                        if ( data['isOpen'] ) {
                            $(row).css('background-color', '#FFFFFF');
                        }
                        else if ( !data['isOpen'] ) {
                            $(row).css('opacity', '0.5');
                            $(row).css('background-color', '#FFFFFF');
                        }
                    },

                    "rowId": "id",
                    "ordering":'true',
                    "order": [2, 'asc'],
                    // Datatables class responsive documentation https://datatables.net/extensions/responsive/classes
                    "columns": [
                        {
                            className: 'not-desktop',
                            orderable: false,
                            data: "dropDownButton",
                        },
                        { "className": "all", "data": "name", "name": "name"},
                        { "className": "all", "data": "start_date", "name": "start_date" },
                        { "className": "desktop", "data": "tags", "name": "tags", "orderable": false, "title":"{!! trans('tag.add_attendance_tag') !!}"},
                        { "className": "desktop", "data": "companions", "name": "companions", "orderable": false, "title":"{!! trans('attendance.companions') !!}"},
                        { "className": "all", "data": "buttons", "name": "buttons", "orderable": false}
                    ],

                    "columnDefs": [
                        { "width": "5%", responsivePriority: 1, "targets": 0 },
                        { "width": "15%", "targets": 1 },
                        { "width": "10%", "targets": 2 },
                        { "width": "50%", "targets": 3 },
                        { "width": "7.5%", "targets": 4 },
                        { "width": "7.5%", "targets": 5 },
                    ],
                    responsive: {
                        details: {
                            renderer: function ( api, rowIdx, columns ) {
                                var columnDefs = api.settings().init().columns;
                                console.log(columnDefs);
                                var data = $.map( columns, function ( col, i ) {
                                    var columnDef = columnDefs[col.columnIndex];
                                    return col.hidden ?
                                    '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                        '<th>'+columnDef.title+'</th>'+
                                    '</tr>'+
                                    '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                        '<td>'+col.data+'</td>'+
                                    '</tr>' :
                                        '';
                                } ).join('');

                                return data ?
                                    $('<table/>').append( data ) :
                                    false;
                            }
                        }
                    },
                    stateSave: true,
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            exportOptions: {columns: [1, 2, 3, 4]}
                        },
                        {
                            extend: 'excelHtml5',
                            exportOptions: {columns: [1, 2, 3, 4]}
                        }
                    ]
                });
            }

            drawAttendanceTable();

            $('#upcoming_events').on('click', '.btn-status', function (e)
            {
                const $this = $(this);
                const id_event = $this.data().id_event;
                let status;

                if($this.hasClass('btn-success')) {
                    status = 2;
                } else if($this.hasClass('btn-danger')) {
                    status = 3;
                } else if($this.hasClass('btn-secondary') || $this.hasClass('btn-warning')) {
                    status = 1;
                }

                $.post( "{{ route('member.edit.event-attendance-status') }}",
                    {
                        'id_event' : id_event,
                        'status': status
                    }).done(function(data) {
                        $("#events_upcoming").DataTable().ajax.reload(null, false );
                    });
            });

            $('#upcoming_events').on('click', '.btn-attendance-option', function () {
                const $this = $(this);
                const id_event = $this.data("event_id");
                let answers = [];

                $(this).closest("td").find('.btn-attendance-option').each(function ($tagId) {
                    const $option = $(this);
                    if ($option.hasClass('btn-success')) {
                        answers.push($option.data("tag_id"));
                    }
                });

                if ($this.hasClass('btn-secondary')) {
                    answers.push($this.data("tag_id"));
                } else {
                    answers = _.without(answers, $this.data("tag_id"));
                }

                $.post("{{ route('member.edit.event-set-answers') }}", {
                    'id_event': id_event,
                    'answers': answers
                }).done(function (data) {
                    $("#events_upcoming").DataTable().ajax.reload(null, false);
                });
            });

            $(document).on('click', '.btn-info', function (event) {
                const eventId = $(event.currentTarget).data("event_id");
                const url = `{{ route('member.get.event-info-modal', ':eventId') }}`.replace(':eventId', eventId);

                $.get(url)
                    .done(function(data) {
                        $('#modalEventInfoContent').html(data);
                        $('#modalEventInfo').modal('show');
                    })
                    .fail(function(xhr) {
                        alert(xhr.responseJSON.message);
                    });
            });

            $(document).on('click', '.btn-google-calendar', function (event) {
                const urlGoogleCalendar = $(event.currentTarget).data("url_google_calendar");
                if (urlGoogleCalendar) {
                    window.open(urlGoogleCalendar, '_blank');
                } else {
                    alert('Google Calendar URL not found');
                }
            });

            $('#upcoming_events').on('change', '.companions', function ()
            {
                const $this = $(this);
                const id_event = $this.data("event_id");
                const companions = $(this).val();

                $.post("{{ route('member.edit.event-set-companions') }}", {
                    'id_event': id_event,
                    'companions': companions
                }).done(function (data) {
                    $("#events_upcoming").DataTable().ajax.reload(null, false);
                });

            });
        });
    </script>
@endsection
