@extends('template.main')

@section('title', trans('general.multievents'))
@section('css_before')
    <style> 
        .btn-action {
            width: 50px;
            height: 35px;
        }
    </style>
    <link rel="stylesheet" href="{!! asset('js/plugins/datatables/dataTables.bootstrap4.css') !!}">
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <style>
        #resetFilter{
            cursor:pointer;
        }
    </style>
@endsection

@section('content')

<div class="block">
    <div class="block-header block-header-default">
      <div class="block-title">
        <h3 class="block-title"><b>{!! trans('general.multievents') !!}</b></h3>
      </div>
      <div class="block-options">
        <div class="btn-group" role="group">
            @if (Auth::user()->can('edit events'))
                <a href="{!! route('multievents.create') !!}" class="btn btn-primary"><i class="fa fa-calendar-plus-o"></i> {!! trans('multievent.add_multievent') !!}</a>
            @endif
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
                    <option value="all" selected>{!! trans('multievent.all') !!}</option>
                    @foreach($tags as $tag)
                        <option value="{!! $tag->getId() !!}">{!! $tag->getName() !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="tags_event_type[]" id="tags_event_type" class=" form-control">
                    <option value=0 selected>{!! trans('multievent.all') !!}</option>
                    @foreach($tags_event_type as $index => $value)
                        <option value="{!! $index !!}">{!! $value !!}</option>
                    @endforeach
                </select>
            </div>
            <div id="filter-icons" class="col-md-2 text-left ">
                <i id="resetFilter" class="fa-solid fa-2x fa-arrow-rotate-right pr-20 text-primary" data-toggle="tooltip" data-placement="right" title="{!! trans('general.tooltip_reset_filter') !!}"></i>
            </div>
        </div>
    </div>



    <div class="block">
    <div class="block-header ">
        <div class="block-title">
            <h3 class="block-title">{!! trans('multievent.upcoming_multievents') !!}</h3>
        </div>
    </div>
    <div class="block-content">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped table-vcenter" style="width: 100%;" id="table-multievents">
                        <thead>
                        <tr>
                            <th>{!! trans('casteller.name') !!}</th>
                            <th>{!! trans('multievent.type') !!}</th>
                            <th>{!! trans('multievent.multievent_tags') !!}</th>
                            <th>{!! trans('multievent.multievent_tags_casteller') !!}</th>
                            <th>{!! trans('general.events') !!}</th>
                            <th>{!! trans('general.actions') !!}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
    </div>
</div>

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
        $(function()
        {
            let tableMultievents;

            function drawEventsTable() {
                tableMultievents = $('#table-multievents').DataTable({
                    "language": {!! trans('datatables.translation') !!},
                    "processing": true,
                    "serverSide": true,
                    "stateSave": true,
                    "stateDuration": -1,
                    "ajax": {
                        "url": "{{ route('multievents.list-ajax') }}",
                        "type": "POST",
                        "data": function(d) {
                            return $.extend({}, d, {
                                "tags": $('#tags').val(),
                                "casteller_tags": $('#casteller_tags').val(),
                                "filter_search_type": $('#filter_search_type').val(),
                                "tags_event_type": $('#tags_event_type').val(),
                            });
                        }
                    },
                    "ordering":'true',
                    "order": [0, 'asc'],
                    "columns": [
                        { "data": "name", "name": "name"},
                        { "data": "type", "name": "type"},
                        { "data": "tags", "name": "tags", "sortable": false},
                        { "data": "casteller_tags", "name": "casteller_tags", "sortable": false},
                        { "data": "events_count", "name": "events_count"},
                        { "data": "buttons", "name": "buttons", "sortable": false}
                    ],
                    "columnDefs": [
                        {"witdh": "20%", "targets": 0},
                        {"width": "10%", "targets": 1},
                        {"width": "15%", "targets": 2},
                        {"width": "15%", "targets": 3},
                        {"width": "10%", "targets": 4},
                        {"width": "10%", "targets": 5}
                    ]
                });
            }

            function initFilters() {
                if (sessionStorage.getItem('multievent_tags_event_type')) {
                    var tags_type = JSON.parse(sessionStorage.getItem('multievent_tags_event_type'));
                    $('#tags_event_type').val(tags_type).trigger('change');
                }

                if (sessionStorage.getItem('multievent_tags')) {
                    var tags = JSON.parse(sessionStorage.getItem('multievent_tags'));
                    $('#tags').val(tags).trigger('change');
                }

                if (sessionStorage.getItem('multievent_filter_search_type')) {
                    $('#filter_search_type').val(sessionStorage.getItem('multievent_filter_search_type')).trigger('change');
                }
            }

            function resetFilters() {
                $("#table-multievents").DataTable().search('');
                sessionStorage.removeItem('multievent_tags_event_type');
                sessionStorage.removeItem('multievent_tags');
                sessionStorage.removeItem('multievent_filter_search_type');
                $('#tags_event_type').val(0).trigger('change');
                $('#tags').val(["all"]).trigger('change');
                $('#filter_search_type').val('OR').trigger('change');
            }

            initFilters();
            drawEventsTable();

            $('.selectize2').select2({
                language: "ca"
            });

            $("#filter_search_type").select2({
                minimumResultsForSearch: -1,
                language: "ca"
            });


            $('#tags_event_type, #tags, #filter_search_type').change(function() {
                tableMultievents.page(0);
                tableMultievents.state.save();

                sessionStorage.setItem('multievent_tags_event_type', JSON.stringify($('#tags_event_type').val()));
                var selectedTags = $('#tags').val();
                sessionStorage.setItem('multievent_tags', JSON.stringify(selectedTags));
                sessionStorage.setItem('multievent_filter_search_type', $('#filter_search_type').val());

                $('#table-multievents').DataTable().destroy();
                drawEventsTable();
            });

            $('#resetFilter').on('click', function(event) {
                resetFilters();
            });
        });
    </script>
@endsection