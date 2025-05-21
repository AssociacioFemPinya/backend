@extends('template.main')

@section('title', trans('general.bbdd'))
@section('css_before')
    <style>
        .agenda {  }

        /* Dates */
        .agenda .agenda-date { width: 170px; }
        .agenda .agenda-date .dayofmonth {
            font-size: 40px;
            float: left;
        }
        .agenda .agenda-date .shortdate {
            font-size: 0.75em;
        }
    </style>
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{!! asset('js/plugins/magnific-popup/magnific-popup.css') !!}">
@endsection

@section('content')

<div class="row">
    <div class="col-lg-3" style="padding-right: 7px;">
        <div class="block">

            <div class="block-content pr-10 pl-10 pt-10">
                <div class="row">
                    <div class="col-md-5 mr-0 pr-0">
                        <a class="img-link img-link-zoom-in" href="{!! $casteller->getProfileImage('xl') !!}">
                            <img class="img-avatar-rounded img-fluid" style="border-radius: 8px;" src="{!! $casteller->getProfileImage() !!}" alt="Avatar: {!! $casteller->getDisplayName() !!}">
                        </a>
                    </div>
                    <div class="col-md-7 pl-5">
                        <p class="text-primary font-w600 mb-5">{!! $casteller->getDisplayName() !!} {!! \App\Helpers\Humans::readCastellerColumn($casteller, 'gender') !!}</p>
                    </div>

                </div>
                <br>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" style="line-height: 2.6">
                            @can('view casteller personals')
                                @if($casteller->getBirthdate())
                                    <div class="col-sm-2 font-w800 text-left"><i class="fa fa-birthday-cake" style="font-size: 20px;"></i></div>
                                    <div class="col-sm-10 font-weight-lighter text-right">{!! \App\Helpers\Humans::readCastellerColumn($casteller, 'birthdate') !!} ({!! $casteller->getAge().' '.trans('casteller.years') !!})</div>
                                @endif

                                @if(!empty($casteller->getEmail()) || !is_null($casteller->getEmail()))
                                    <div class="col-sm-2 font-w800 text-left"><i class="fa fa-envelope-o" style="font-size: 20px;"></i></div>
                                    <div class="col-sm-10 font-weight-lighter text-right">{!! $casteller->getEmail() !!}</div>
                                @endif

                                @if(!empty($casteller->getEmail2()) || !is_null($casteller->getEmail2()))
                                    <div class="col-sm-2 font-w800 text-left"><i class="fa fa-envelope-o" style="font-size: 20px;"></i></div>
                                    <div class="col-sm-10 font-weight-lighter text-right">{!! $casteller->getEmail2() !!}</div>
                                @endif

                                @if(!empty($casteller->getPhoneMobile()) || !is_null($casteller->getPhoneMobile()))
                                    <div class="col-sm-2 text-secondary text-left"><i class="fa fa-mobile-phone" style="font-size: 29px;"></i></div>
                                    <div class="col-sm-10 font-weight-lighter text-right">{!! $casteller->getPhoneMobile() !!}</div>
                                @endif

                                @if(!empty($casteller->getPhone()) || !is_null($casteller->getPhone()))
                                    <div class="col-sm-2 text-secondary text-left"><i class="fa fa-phone" style="font-size: 23px;"></i></div>
                                    <div class="col-sm-10 font-weight-lighter text-right">{!! $casteller->getPhone() !!}</div>
                                @endif

                                @if(!empty($casteller->getPhoneEmergency()) || !is_null($casteller->getPhoneEmergency()))
                                    <div class="col-sm-3 text-secondary text-left text-danger"><i class="fa fa-phone" style="font-size: 23px;"></i><sup style="top: -0.9em;"><i class="fa fa-plus" style="font-size: 13px; margin-left: -4px;"></i></sup></div>
                                    <div class="col-sm-9 font-weight-lighter text-right">{!! $casteller->getPhoneEmergency() !!}</div>
                                @endif

                                @if(!empty($casteller->getNationalIdNumber()) || !is_null($casteller->getNationalIdNumber()))
                                    <div class="col-sm-2 text-secondary text-left"><i class="fa fa-id-card-o" style="font-size: 23px;"></i></div>
                                    <div class="col-sm-10 font-weight-lighter text-right">{!! $casteller->getNationalIdNumber() !!}</div>
                                @endif

                                @if((!empty($casteller->getAddress()) && !empty($casteller->getZipCode()) && !empty($casteller->getCity()) && !empty($casteller->getComarca())) || (!is_null($casteller->getAddress()) && !is_null($casteller->getZipCode()) && !is_null($casteller->getCity()) && !is_null($casteller->getComarca())))
                                    <div class="col-sm-2 text-secondary text-left"><i class="fa fa-address-book-o" style="font-size: 22px;"></i></div>
                                    <div class="col-sm-10 font-weight-lighter text-right">{!! $casteller->getAddress() !!}<br></div>
                                    <div class="col-sm-12 font-weight-lighter text-right" style="line-height: 9px;">{!! $casteller->getZipCode() !!}, {!! $casteller->getCity() !!}</div>
                                    <div class="col-sm-12 font-weight-lighter text-right">{!! $casteller->getComarca() !!}</div>
                                @endif
                            @endcan

                            @if (Auth::user()->getColla()->getConfig()->getBoardsEnabled() && $casteller->getPosition())
                                <div class="col-sm-12 text-secondary text-left">
                                    <b>{!! trans('casteller.position') !!}</b>

                                    <p><span class="badge badge-info">{!! $casteller->getPosition()->getName() !!}</span></p>
                                </div>
                            @endif

                            @if(!empty(\App\Helpers\Humans::readCastellerColumn($casteller, 'tags', 'left')))
                            <div class="col-md-12 text-secondary text-left"><b>{!! trans('general.tags') !!}</b></div>
                            <div class="col-md-12">
                                {!! \App\Helpers\Humans::readCastellerColumn($casteller, 'tags', 'left'); !!}

                            </div>
                            @endif

                            <div class="col-md-12">
                                <hr>
                            </div>

                            <div class="col-md-12">
                                <ul class="nav nav-pills flex-column list-group" style="padding-bottom: 10px;" id="profileList">

                                    @can('view BBDD')
                                        <li class="nav-item">
                                            <a class="nav-link d-flex align-items-center justify-content-between list-group-item-action lnk-profile" data-tab="edit" href="">
                                                <span><i class="fa fa-pencil mr-5"></i> {!! trans('general.edit') !!}</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('view BBDD')
                                        <li class="nav-item">
                                            <a class="nav-link d-flex align-items-center justify-content-between list-group-item-action lnk-profile" data-tab="attendance" href="">
                                                <span><i class="fa fa-calendar mr-5"></i> {!! trans('general.attendance') !!}</span>
                                            </a>
                                        </li>
                                    @endcan

                                    {{-- @if(Auth::user()->getColla()->getConfig()->getBoardsEnabled())
                                        @can('view boards')
                                            <li class="nav-item">
                                                <a class="nav-link d-flex align-items-center justify-content-between list-group-item-action lnk-profile" href="">
                                                    <span><img src="{{asset('media/img/ico_pinya3.svg')}}" class="mr-5" style="width: 16px;" alt=""> {!! trans('general.pinyes') !!}</span>
                                                </a>
                                            </li>
                                        @endcan
                                     @endif --}}
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-9" style="padding-left: 7px;">
        <div class="block" id="profileBlock">
            <div class="block-header block-header-default">
                <h3 class="block-title" id="profileTitle"><span><i class="fa fa-pencil mr-5"></i> {!! trans('general.edit') !!}</span></h3>
            </div>
            <div class="block-content" id="profileContent">
                @include('castellers.ajax.card-edit')
            </div>
        </div>
    </div>
</div>

<!-- START - MODAL DELETE -->
<div class="modal fade" id="modalDelCasteller" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-popin" role="document">
        <div class="modal-content">
            {!! Form::open(array('url' => route('castellers.destroy', $casteller->getId()), 'method' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'fromDelCasteller')) !!}
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
                    <p class="text-muted">{!! trans('casteller.del_casteller_warning') !!}</p>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::submit(trans('general.delete') , array('class' => 'btn btn-danger')) !!}
                <button type="button" class="btn  btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
            </div>
            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!--/ END - MODAL DELETE -->
@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <script src="{!! asset('js/plugins/magnific-popup/jquery.magnific-popup.js') !!}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
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

            // GENERAL

            $('#profileList').on('click', '.lnk-profile', function(e)
            {
                e.preventDefault();
                $('#profileBlock').addClass('block-mode-loading');
                var title =  $(this).html();
                $('#profileList .bg-primary-lighter').removeClass('bg-primary-lighter');
                $(this).addClass('bg-primary-lighter');
                $('#profileTitle').html(title);

                var tab = $(this).data().tab;
                var url = null;

                if(tab === 'edit') {

                    url = "{{ route('castellers.edit.card-edit', $casteller->getId()) }}";

                } else if(tab === 'attendance') {

                    url = "{{ route('castellers.edit.card-attendance', $casteller->getId()) }}";
                }

                if(url)
                {
                    $.get( url, function() {

                    }).then(function(result){
                        $('#profileBlock').removeClass('block-mode-loading');
                        $('#profileContent').html( result );

                        if(tab==='edit') {
                            $('#profileContent .tags').select2({language: "ca"});
                            disableFields();
                            // we need to reactivate the tabs when loading the content
                            $("#tabs a").click(function(e){
                                e.preventDefault();
                                $(this).tab("show");
                            });
                        }else{
                            drawAttendanceTable();

                            $('.selectize2').select2({language: "ca"});

                            $('#attendance_tab #tags, #attendance_tab #filter_search_type, #attendance_tab #search_period, #attendance_tab #tags_event_type').change(function(){

                                $('#events_upcoming').DataTable().destroy();
                                $('#events_past').DataTable().destroy();
                                drawAttendanceTable();
                            });

                            $("#filter_search_type").select2({
                                minimumResultsForSearch: -1,
                                language: "ca"
                            });
                        }

                    });
                }
            });


            // EDIT TAB

            $('#profileContent .tags').select2({language: "ca"});


            $("#profileContent").on('click', '.btn-delete-casteller', function (e)
            {
                e.preventDefault();
                $('#modalDelCasteller').modal('show');
            });

            $('.img-link').magnificPopup({type:'image'});

            $("#profileContent .family").select2({
                tags: true,
                language: "ca"
            });

            $('#photo').on('change',function(){
                //get the file name
                let fieldVal = $(this).val();

                // Change the node's value by removing the fake path (Chrome)
                fieldVal = fieldVal.replace("C:\\fakepath\\", "");

                //replace the "Choose a file" label
                $(this).next('.custom-file-label').html(fieldVal);
            });

            $("#birthdate, #subscription_date").datepicker({
                @if (Auth()->user()->getLanguage() === 'ca')
                language: 'ca',
                @elseif(Auth()->user()->getLanguage() === 'es')
                language: 'es',
                @endif
                format: "dd/mm/yyyy",
                autoclose: true

            });

            $('#profileBlock').on('click', '.btn-status-verified', function (e)
            {

                var id_casteller = $(this).data().id_casteller;
                var status;

                if($(this).hasClass('btn-success'))
                {
                    status = 2;
                    $(this).removeClass( "btn-success" );
                    $(this).addClass( "btn-danger" );
                    $(this).html('<i class="fa-solid fa-close"></i>');
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
                }

                setStatusVerified(id_casteller, status);

            });

            $('#profileBlock').on('click', '.btn-status', function (e)
            {

                var id_casteller = $(this).data().id_casteller;
                var status;

                if($(this).hasClass('btn-success'))
                {
                    status = 2;
                    $(this).removeClass( "btn-success" );
                    $(this).addClass( "btn-danger" );
                    $(this).html('<i class="fa-solid fa-close"></i>');
                }
                else if($(this).hasClass('btn-danger'))
                {
                    status = 3;
                    $(this).removeClass( "btn-danger" );
                    $(this).addClass( "btn-secondary" );
                    $(this).html('<i class="fa-solid fa-question"></i>');
                }
                else if($(this).hasClass('btn-secondary') || $(this).hasClass('btn-outline-warning'))
                {
                    status = 1;
                    $(this).removeClass( "btn-secondary" );
                    $(this).addClass( "btn-success" );
                    $(this).html('<i class="fa-solid fa-check"></i>');
                }

                setStatus(id_casteller, status);

            });

            $('#tabs a').on('click', function (e)
            {
                setActiveTab($(this).data().tab);
            });

            disableFields();

            function disableFields()
            {
                @if(Auth::user()->can('view casteller personals') && !Auth::user()->can('edit casteller personals'))
                $('#btabs-static-profile .form-control').each(function() {
                    $(this).prop("disabled", true);
                });
                @endif

                @if(Auth::user()->can('view BBDD') && !Auth::user()->can('edit BBDD') )
                $('#btabs-static-home .form-control').each(function() {
                    $(this).prop("disabled", true);
                });
                @endif

            }

            function setActiveTab(active_tab){
                $('#active_tab').val(active_tab);
            }

            // ATTENDANCE TAB


            function drawAttendanceTable()
            {
                var events_upcoming = $("#events_upcoming").DataTable({
                    "language": {!! trans('datatables.translation') !!},
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{ route('castellers.edit.card-attendance-events', [$casteller->getId(), 'upcoming']) }}",
                        "type": "POST",
                        "data": function ( d ) {
                            return $.extend( {}, d, {
                                "tags": $('#attendance_tab #tags').val(),
                                "filter_search_type": $('#attendance_tab #filter_search_type').val(),
                                "tags_event_type": $('#attendance_tab #tags_event_type').val(),
                                "search_period": $('#search_period').val(),
                            } );
                        }
                    },
                    "ordering":'true',
                    "order": [3, 'asc'],
                    "columns": [
                        { "data": "name", "name": "name"},
                        { "data": "type", "name": "type"},
                        { "data": "tags", "name": "tags", "orderable": false },
                        { "data": "start_date", "name": "start_date" },
                        { "data": "status", "name": "status", "orderable": false },
                        { "data": "status_verified", "name": "status_verified", "orderable": false }
                    ],
                    "columnDefs": [
                        { "width": "30%", "targets": 0 },
                        { "width": "15%", "targets": 1 },
                        { "width": "20%", "targets": 2 },
                        { "width": "20%", "targets": 3 },
                        { "width": "7%", "targets": 4 },
                        { "width": "7%", "targets": 5 },
                    ],
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            exportOptions: {columns: [0, 1, 2, 3, 4]}
                        },
                        {
                            extend: 'excelHtml5',
                            exportOptions: {columns: [0, 1, 2, 3, 4]}
                        }
                    ]
                });

                var events_past = $("#events_past").DataTable({
                    "language": {!! trans('datatables.translation') !!},
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{ route('castellers.edit.card-attendance-events', [$casteller->getId(), 'past']) }}",
                        "type": "POST",
                        "data": function ( d ) {
                            return $.extend( {}, d, {
                                "tags": $('#attendance_tab #tags').val(),
                                "filter_search_type": $('#attendance_tab #filter_search_type').val(),
                                "tags_event_type": $('#attendance_tab #tags_event_type').val(),
                                "search_period": $('#search_period').val(),
                            } );
                        }
                    },
                    "ordering":'true',
                    "order": [3, 'asc'],
                    "columns": [
                        { "data": "name", "name": "name"},
                        { "data": "type", "name": "type"},
                        { "data": "tags", "name": "tags", "orderable": false },
                        { "data": "start_date", "name": "start_date" },
                        { "data": "status", "name": "status", "orderable": false },
                        { "data": "status_verified", "name": "status_verified", "orderable": false }

                    ],
                    "columnDefs": [
                        { "width": "30%", "targets": 0 },
                        { "width": "15%", "targets": 1 },
                        { "width": "20%", "targets": 2 },
                        { "width": "20%", "targets": 3 },
                        { "width": "7%", "targets": 4 },
                        { "width": "7%", "targets": 5 },
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
                    ]
                });
            }

            drawAttendanceTable();


            function setStatus(id_event, status)
            {
                $.post( "{{ route('event.attendance.set-status') }}",
                    { 'id_casteller': {!! $casteller->getId() !!},
                        'id_event' : id_event,
                        'status': status});
            }

            function setStatusVerified(id_event, status)
            {
                $.post( "{{ route('event.attendance.set-status-verified') }}",
                    { 'id_casteller': {!! $casteller->getId() !!},
                        'id_event' : id_event,
                        'status': status});
            }


        });
    </script>
@endsection
