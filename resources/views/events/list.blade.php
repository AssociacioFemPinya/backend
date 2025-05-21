@extends('template.main')

@section('title', trans('general.attendance'))
@section('css_before')
    <style>
        #resetFilter {
            cursor: pointer;
        }
    </style>
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
    <link href="{{ asset('css/modals/action_buttons_datatables.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="block">

    <div class="block-header block-header-default">
        <div class="block-title">
            <h3 class="block-title"><b>{!! trans('general.events') !!}</b></h3>
        </div>
        <div class="block-options">


            <div class="btn-group" role="group">
                @can('edit events')
                    <button type="button" class="btn btn-primary dropdown-toggle" id="btnGroupDrop1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-calendar-plus-o"></i> {!! trans('event.add_events') !!}</button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item" href="{!! route('events.create') !!}">
                            {!! trans('event.add_event') !!}
                        </a>
                        <a class="dropdown-item" href="{!! route('events.create-group') !!}">
                            {!! trans('event.add_multiple_events') !!}
                        </a>
                    </div>
                @endcan
            </div>

        </div>
    </div>

    <div class="block-content block-content-full">
        <div class="row">
            <div class="col-md-1">
                <label class="control-label" style="padding-top: 5px;">
                    {!! trans('general.filter') !!}
                </label>
            </div>

            <div class="col-md-1">
                <select name="filter_search_type" id="filter_search_type" class="selectize2 form-control">
                    <option value="AND">AND</option>
                    <option value="OR" selected>OR</option>
                </select>
            </div>
            <div class="col-md-4">
                <select name="tags[]" id="tags" class="selectize2 form-control" multiple>
                    <option value="all" selected>{!! trans('event.all') !!}</option>
                    @foreach($tags as $tag)
                        <option value="{!! $tag->getId() !!}">{!! $tag->getName() !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="tags_event_type[]" id="tags_event_type" class=" form-control">
                    <option value=0 selected>{!! trans('event.all') !!}</option>
                    @foreach($tags_event_type as $index => $value)
                        <option value="{!! $index !!}">{!! $value !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="search_period" id="search_period" class=" form-control">
                    @foreach($periods as $period)
                        <option value="{!! $period->getId() !!}" @if(isset($currentPeriod) && ($period == $currentPeriod)) selected @endif>{!! $period->getName() !!}</option>
                    @endforeach
                    <option value="0" @if(!isset($currentPeriod)) selected @endif>{!! trans('event.all') !!}</option>
                </select>
            </div>
            <div id="filter-icons" class="col-md-2 text-left ">
                <i id="resetFilter" class="fa-solid fa-2x fa-arrow-rotate-right pr-20 text-primary" data-toggle="tooltip" data-placement="right" title="{!! trans('general.tooltip_reset_filter') !!}"></i>
            </div>
        </div>
    </div>
</div>

<div class="block">
    <div class="block-header ">
        <div class="block-title">
            <h3 class="block-title">{!! trans('event.upcoming_events') !!}</h3>
        </div>
    </div>
    <div class="block-content">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped table-vcenter" style="width: 100%;" id="events_upcoming">
                        <thead>
                        <tr>
                            <th>{!! trans('general.name') !!}</th>
                            <th>{!! trans('general.type') !!}</th>
                            <th>{!! trans('general.tags') !!}</th>
                            <th>{!! trans('event.event_tags_casteller') !!}</th>
                            <th>{!! trans('general.date') !!}</th>
                            <th>#</th>
                        </tr>
                        </thead>
                    </table>
                </div>
    </div>
</div>

<div class="block">
    <div class="block-header ">
        <div class="block-title">
            <h3 class="block-title">{!! trans('event.past_events') !!}</h3>
        </div>
    </div>
    <div class="block-content">
        <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped table-vcenter" style="width: 100%;" id="events_past">
                    <thead>
                    <tr>
                        <th>{!! trans('general.name') !!}</th>
                        <th>{!! trans('general.type') !!}</th>
                        <th>{!! trans('general.tags') !!}</th>
                        <th>{!! trans('event.event_tags_casteller') !!}</th>
                        <th>{!! trans('general.date') !!}</th>
                        <th>#</th>
                    </tr>
                    </thead>
                </table>
        </div>
    </div>
</div>

@include('events.modals.modal-attach-board')

@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/buttons.html5.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.js') }}"></script>

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
                const token = $('meta[name="csrf-token"]').attr('content'); // or _token, whichever you are using

                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token); // adds directly to the XmlHttpRequest Object
                }
            });
        });
    </script>
    <script>
        $(function () {

            let currentPeriod = parseInt({{ (isset($currentPeriod)) ? $currentPeriod->getId() : '0' }});
            let events_upcoming;
            let events_past;

            function drawEventsTable()
            {
                events_upcoming = $("#events_upcoming").DataTable({
                    "language": {!! trans('datatables.translation') !!},
                    "processing": true,
                    "serverSide": true,
                    "stateSave": true,
                    "stateDuration": -1,
                    "ajax": {
                        "url": "{{ route('events.list-ajax', 'upcoming') }}",
                        "type": "POST",
                        "data": function ( d ) {
                            return $.extend( {}, d, {
                                "tags": $('#tags').val(),
                                "casteller_tags": $('#casteller_tags').val(),
                                "filter_search_type": $('#filter_search_type').val(),
                                "tags_event_type": $('#tags_event_type').val(),
                                "search_period": $('#search_period').val(),
                            } );
                        }
                    },
                    "ordering":'true',
                    "order": [4, 'asc'],
                    "columns": [
                        { "data": "name", "name": "name"},
                        { "data": "type", "name": "type"},
                        { "data": "tags", "name": "tags", "orderable": false },
                        { "data": "casteller_tags", "name": "casteller_tags", "orderable": false },
                        { "data": "start_date", "name": "start_date" },
                        { "data": "buttons", "name": "buttons", "orderable": false }
                    ],
                    "columnDefs": [
                        { "width": "27%", "targets": 0 },
                        { "width": "10%", "targets": 1 },
                        { "width": "15%", "targets": 2 },
                        { "width": "15%", "targets": 3 },
                        { "width": "20%", "targets": 4 },
                        { "width": "210", "targets": 5 },
                    ]
                });

                events_past = $("#events_past").DataTable({
                    "language": {!! trans('datatables.translation') !!},
                    "processing": true,
                    "serverSide": true,
                    "stateSave": true,
                    "stateDuration": -1,
                    "ajax": {
                        "url": "{{ route('events.list-ajax', 'past') }}",
                        "type": "POST",
                        "data": function ( d ) {
                            return $.extend( {}, d, {
                                "tags": $('#tags').val(),
                                "casteller_tags": $('#casteller_tags').val(),
                                "filter_search_type": $('#filter_search_type').val(),
                                "tags_event_type": $('#tags_event_type').val(),
                                "search_period": $('#search_period').val(),
                            } );
                        }
                    },
                    "ordering":'true',
                    "order": [4, 'desc'],
                    "columns": [
                        { "data": "name", "name": "name"},
                        { "data": "type", "name": "type"},
                        { "data": "tags", "name": "tags", "orderable": false },
                        { "data": "casteller_tags", "name": "casteller_tags", "orderable": false },
                        { "data": "start_date", "name": "start_date" },
                        { "data": "buttons", "name": "buttons", "orderable": false }
                    ],
                    "columnDefs": [
                        { "width": "27%", "targets": 0 },
                        { "width": "10%", "targets": 1 },
                        { "width": "15%", "targets": 2 },
                        { "width": "15%", "targets": 3 },
                        { "width": "20%", "targets": 4 },
                        { "width": "210", "targets": 5 },
                    ]
                });
            }

            function initFilters(){

                if (sessionStorage.getItem('event_filter_search_period')) {
                    var tags = JSON.parse(sessionStorage.getItem('event_filter_search_period'));
                    $('#search_period').val(tags).trigger('change');
                }

                if (sessionStorage.getItem('event_tags_event_type')) {
                    var tags = JSON.parse(sessionStorage.getItem('event_tags_event_type'));
                    $('#tags_event_type').val(tags).trigger('change');
                }

                if (sessionStorage.getItem('event_tags')) {
                    var tags = JSON.parse(sessionStorage.getItem('event_tags'));
                    $('#tags').val(tags).trigger('change');
                }
                if (sessionStorage.getItem('event_filter_search_type')) {
                    $('#filter_search_type').val(sessionStorage.getItem('event_filter_search_type')).trigger('change');
                }
            }

            function resetFilters(){
                $("#events_upcoming").DataTable().search('');
                $("#events_past").DataTable().search('');
                sessionStorage.removeItem('event_filter_search_period');
                sessionStorage.removeItem('event_tags_event_type');
                sessionStorage.removeItem('event_tags');
                sessionStorage.removeItem('event_filter_search_type');
                $('#tags').val(["all"]).trigger('change');
                $('#filter_search_type').val('OR').trigger('change');
                $('#search_period').val(currentPeriod).trigger('change');
                $('#tags_event_type').val(0).trigger('change');
            }

            initFilters();
            drawEventsTable();

            $('#tags, #filter_search_type, #search_period, #tags_event_type').change(function(){

                events_upcoming.page(0);
                events_upcoming.state.save();
                events_past.page(0);
                events_past.state.save();
                var selectedTags = $('#tags').val();
                sessionStorage.setItem('event_tags', JSON.stringify(selectedTags));
                sessionStorage.setItem('event_filter_search_type', $('#filter_search_type').val());
                sessionStorage.setItem('event_filter_search_period', $('#search_period').val());
                sessionStorage.setItem('event_tags_event_type', $('#tags_event_type').val());

                $('#events_upcoming').DataTable().destroy();
                $('#events_past').DataTable().destroy();
                drawEventsTable();
            });

            $('.selectize2').select2({language: "ca"});

            $("#filter_search_type").select2({
                minimumResultsForSearch: -1,
                language: "ca"
            });


            @if(Auth::user()->getColla()->getConfig()->getBoardsEnabled())
                $('#events_upcoming, #events_past').on('click', '.btn-attach-board', function(){

                    let eventId = $(this).data('event_id');
                    let url = "{{ route('event.board.attach', ['event' => ':eventId']) }}";
                    url = url.replace(':eventId', eventId);

                    $('#formAttachBoard').attr('action', url);
                    $('#modalAttachBoard').modal('show');
                });
            @endif

            $('#resetFilter').on('click', function (event)
            {
                resetFilters();
            });


        });
    </script>
@endsection
