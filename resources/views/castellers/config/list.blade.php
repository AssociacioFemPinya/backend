@extends('template.main')

@section('title', trans('general.bbdd'))
@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
    <link rel="stylesheet" href="{!! asset('js/plugins/cloudflare-switchery/css/switchery.min.css') !!}">

    <style>
        .custom-file-input ~ .custom-file-label::after {
            content: "{!! trans('admin.btn_input_file_text') !!}";
        }

        #castellers td{
            cursor:pointer;
        }
    </style>
@endsection

@section('content')

<div class="block">

    <div class="block-header block-header-default">

        <div class="block-title">
            <h3 class="block-title"><b>{!! trans('general.castellers') !!}</b></h3>
        </div>

    </div>

    <div class="block-content block-content-full">
        <div class="row">
            @include('components.castellers-filter-header')
        </div>
        <div class="row" style="padding-top: 25px;">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped table-condensed" style="width: 100%;" id="castellers">
                        <thead>
                        <tr>
                            <th></th>
                            <th>{!! trans('casteller.alias') !!}</th>
                            <th>{!! trans('general.tags') !!}</th>
                            <th>{!! trans('casteller.telegram_enabled') !!}</th>
                            <th>{!! trans('casteller.auth_token_enabled') !!}</th>
                            <?php //<th>{!! trans('casteller.api_token_enabled') !!}</th> ?>
                            <th>{!! trans('casteller.tecnica') !!}</th>
                            <th>{!! trans('casteller.last_acces') !!}</th>
                            <th>{!! trans('casteller.last_credentials_sent') !!}</th>
                            <th>#</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- START - Modal long -->
<div class="modal fade" id="modalCredentialsMail" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
        <div class="modal-content" id="modalCredentialsMailContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>

<!-- END - Modal long -->
@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/cloudflare-switchery/js/switchery.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

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
            var castellers;
            function drawCastellersTable()
            {
                castellers = $("#castellers").DataTable({
                    "language": {!! trans('datatables.translation') !!},
                    "processing": true,
                    "serverSide": true,
                    "pageLength": 25,
                    "stateSave": true,
                    "stateDuration": -1,
                    "ajax": {
                        "url": "{{ route('castellers.config.list-ajax') }}",
                        "type": "POST",
                        "data": function ( d ) {
                            return $.extend( {}, d, {
                                "tags": $('#tags').val(),
                                "status": $('#status').val(),
                                "filter_search_type": $('#filter_search_type').val(),
                            } );
                        }
                    },
                    "ordering":'true',
                    "order": [1, 'asc'],
                    "columns": [
                        { "data": "photo", "name": "photo", "orderable": false },
                        { "data": "alias", "name": "alias"},
                        { "data": "tags", "name": "tags", "orderable": false },
                        { "data": "telegram_enabled", "name": "telegram_enabled", "orderable": false },
                        { "data": "auth_token_enabled", "name": "auth_token_enabled", "orderable": false },
                        //{ "data": "api_token_enabled", "name": "api_token_enabled", "orderable": false },
                        { "data": "tecnica", "name": "tecnica", "orderable": false },
                        { "data": "last_access_at", "name": "last_access_at", "orderable": false },
                        { "data": "last_credentials_sent_at", "name": "last_credentials_sent_at", "orderable": false },
                        { "data": "buttons", "name": "buttons", "orderable": false },
                    ],
                    "columnDefs": [
                        // { "width": "5%", "targets": 0 },
                        // { "width": "30%", "targets": 1 },
                    ],
                    "drawCallback": function( settings ) {
                        var api = this.api();

                        $('#totalCastellers').html(api.page.info().recordsTotal);

                        let elems = Array.prototype.slice.call(document.querySelectorAll('.js-switchery'));

                        @if(!Auth::user()->can('edit casteller config'))
                            elems.forEach(function (html) {
                                let elem = new Switchery(html, {size: 'small'});
                                elem.disable();
                            });
                        @else
                        elems.forEach(function (html) {
                            new Switchery(html, {size: 'small'});
                        });
                        @endif
                    }
                });

            }

            function initFilters(){

                if (sessionStorage.getItem('casteller_config_tags')) {
                    var tags = JSON.parse(sessionStorage.getItem('casteller_config_tags'));
                    $('#tags').val(tags).trigger('change');
                }
                if (sessionStorage.getItem('casteller_config_status')) {
                    $('#status').val(sessionStorage.getItem('casteller_config_status')).trigger('change');
                }
                if (sessionStorage.getItem('casteller_config_filter_search_type')) {
                    $('#filter_search_type').val(sessionStorage.getItem('casteller_config_filter_search_type')).trigger('change');
                }
            }

            function resetFilters(){
                $("#castellers").DataTable().search('');
                sessionStorage.removeItem('casteller_config_tags');
                sessionStorage.removeItem('casteller_config_status');
                sessionStorage.removeItem('casteller_config_filter_search_type');
                $('#tags').val(["all"]).trigger('change');
                $('#status').val(7).trigger('change');
                $('#filter_search_type').val('OR').trigger('change');
            }

            initFilters();
            drawCastellersTable();

            @can('edit casteller config')

                function setStatus(id_casteller, status, fieldname)
                {
                    $.post( "{{ route('castellers.config.set-status') }}",
                        {   'id_casteller': id_casteller,
                            'status': status,
                            'fieldname': fieldname,
                    });
                }

                $('#castellers').on('change', '.js-switchery', function ()
                {

                    var status;
                    var id_casteller = $(this).data().id_casteller;

                    if($(this).hasClass('telegram_enabled')){
                        var fieldname = 'telegram_enabled'
                    }else if($(this).hasClass('auth_token_enabled')){
                        var fieldname = 'auth_token_enabled'
                    }else if($(this).hasClass('api_token_enabled')){
                        var fieldname = 'api_token_enabled'
                    }else if($(this).hasClass('tecnica')){
                        var fieldname = 'tecnica'
                    }else if($(this).hasClass('last_access_at')){
                        var fieldname = 'last_access_at'
                    }else if($(this).hasClass('last_credentials_sent_at')){
                        var fieldname = 'last_credentials_sent_at'
                    }

                    if($(this).hasClass('active'))
                    {
                        $(this).removeClass( "active" );
                        $(this).addClass( "inactive" );
                        status = 0;
                    }
                    else if($(this).hasClass('inactive'))
                    {
                        $(this).removeClass( "inactive" );
                        $(this).addClass( "active" );
                        status = 1;
                    }
                    setStatus(id_casteller, status, fieldname);
                });


            @endcan

            $('#tags, #status, #filter_search_type').change(function(){

                castellers.page(0);
                castellers.state.save();
                var selectedTags = $('#tags').val();
                sessionStorage.setItem('casteller_config_tags', JSON.stringify(selectedTags));
                sessionStorage.setItem('casteller_config_status', $('#status').val());
                sessionStorage.setItem('casteller_config_filter_search_type', $('#filter_search_type').val());

                $('#castellers').DataTable().destroy();
                drawCastellersTable();
            });

            $('.selectize2, .tags_add').select2({language: "ca"});

            $('#photo').on('change',function(){
                //get the file name
                var fieldVal = $(this).val();

                // Change the node's value by removing the fake path (Chrome)
                fieldVal = fieldVal.replace("C:\\fakepath\\", "");

                //replace the "Choose a file" label
                $(this).next('.custom-file-label').html(fieldVal);
            });

            $(document).on('click', '.btn-mail', function (event)
            {
                var castellerId = $(event.currentTarget).data("casteller_id");
                var url = "{{ route('castellers.config.credentials-mail-modal', ':castellerId') }}";
                url = url.replace(':castellerId', castellerId);
                $.get( url, function( data ) {
                        $('#modalCredentialsMailContent').html( data );
                        $('#modalCredentialsMail').modal('show');
                }).fail(function( xhr, textStatus, errorThrown ) {
                    alert(xhr.responseJSON.message);
                });
            });

            $(document).on('click', '.sendEmail', function (event)
            {
                var castellerId = $(event.currentTarget).data("casteller_id");
                var url = "{{ route('castellers.config.send-credentials-mail', ':castellerId') }}";
                url = url.replace(':castellerId', castellerId);
                $.get( url, function( data ) {
                        $('#modalCredentialsMail').modal('hide');
                });
            });

            $('#resetFilter').on('click', function (event)
            {
                resetFilters();
            });

        });
    </script>
@endsection
