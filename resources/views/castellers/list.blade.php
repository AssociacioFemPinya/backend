@extends('template.main')

@section('title', trans('general.bbdd'))
@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">

    <style>
        .custom-file-input ~ .custom-file-label::after {
            content: "{!! trans('admin.btn_input_file_text') !!}";
        }

        #castellers td{
            cursor:pointer;
        }
        
        /* Responsive button styles */
        @media (max-width: 767.98px) {
            .btn-responsive-text {
                padding: 0.375rem 0.5rem;
                font-size: 0.875rem;
            }
            .btn-icon-only {
                width: 38px;
                padding-left: 0;
                padding-right: 0;
            }
            .btn-responsive-text .btn-text {
                display: none;
            }
        }
    </style>
@endsection

@section('content')

<div class="block">

    <div class="block-header block-header-default">

        <div class="block-title">
            <h3 class="block-title"><b>{!! trans('general.castellers') !!}</b></h3>
        </div>
        <div class="block-options">
            @can('edit BBDD')
                <button class="btn btn-primary btn-responsive-text" data-toggle="tooltip" title="{!! trans('casteller.add_casteller') !!}">
                    <i class="fa fa-user-plus"></i> 
                    <span class="btn-text d-none d-md-inline-block">{!! trans('casteller.add_casteller') !!}</span>
                </button>
            @endcan

            @can('edit casteller personals')
                <button class="btn btn-primary btn-responsive-text ml-1" data-toggle="tooltip" title="{!! trans('casteller.add_casteller_excel') !!}">
                    <i class="fa fa-file-import"></i> 
                    <span class="btn-text d-none d-md-inline-block">{!! trans('casteller.add_casteller_excel') !!}</span>
                </button>
            @endcan

            @can('view casteller personals')
                <div class="btn-group ml-1">
                    <button type="button" class="btn btn-primary btn-responsive-text dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-download"></i> 
                        <span class="btn-text d-none d-md-inline-block">{!! trans('casteller.download_castellers_excel') !!}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item btn-export-castellers" href=javascript:void(0);>
                            <i class="fa fa-file-excel-o mr-1"></i> Excel
                        </a>
                        <a class="dropdown-item btn-export-castellersods" href=javascript:void(0);>
                            <i class="fa fa-file-text-o mr-1"></i> ODS
                        </a>
                    </div>
                </div>
            @endcan

        </div>
    </div>


    <div class="block-content block-content-full">
        <div class="row">
            @include('components.castellers-filter-header')
        </div>

        <div class="table-responsive">
            <x-data-table :datatable="$datatable"/>
        </div>
    </div>
</div>

<!-- START - Modal Add Casteller -->
<div class="modal fade" id="modalAddCasteller" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
        <div class="modal-content" id="modalAddCastellerContent">
            <!-- MODAL CONTENT -->
            @include('castellers.modals.modal-add')
        </div>
    </div>
</div>
<!-- END - Modal Add Casteller -->

<!-- START - Modal Add Casteller -->
<div class="modal fade" id="modalAddCastellerExcel" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
        <div class="modal-content" id="modalAddCastellerExcelContent">
            <!-- MODAL CONTENT -->
            @include('castellers.modals.modal-add-excel')
        </div>
    </div>
</div>
<!-- END - Modal Add Casteller -->

@endsection

@section('js')
@stack('datatables_js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <!-- Page JS Plugins -->
    <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    @if (Auth()->user()->getLanguage() === 'ca')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.ca.min.js') !!}"></script>
    @elseif(Auth()->user()->getLanguage() === 'es')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js') !!}"></script>
    @endif

    <script>
        function datatablesDrawCallback() {
            let api = this.api();
            $('#totalCastellers').html(api.page.info().recordsTotal);
        };

        // Init filters
        $(function () {
            if (sessionStorage.getItem('casteller_tags')) {
                var tags = JSON.parse(sessionStorage.getItem('casteller_tags'));
                $('#tags').val(tags).trigger('change');
            }
            if (sessionStorage.getItem('casteller_status')) {
                $('#status').val(sessionStorage.getItem('casteller_status')).trigger('change');
            }
            if (sessionStorage.getItem('casteller_filter_search_type')) {
                $('#filter_search_type').val(sessionStorage.getItem('casteller_filter_search_type')).trigger('change');
            }
        });
        function resetFilters(){
            $("#castellers").DataTable().search('');
            sessionStorage.removeItem('casteller_tags');
            sessionStorage.removeItem('casteller_status');
            sessionStorage.removeItem('casteller_filter_search_type');
            $('#tags').val(["all"]).trigger('change');
            $('#status').val({{  \App\Enums\CastellersStatusEnum::ALL }}).trigger('change');
            $('#filter_search_type').val('OR').trigger('change');
        };
        $(function () {
            // On row click, go to edit Casteller
            $('#castellers tbody').on( 'click', 'td', function (event) {

                event.preventDefault();

                let id_casteller = $("#castellers").DataTable().row( $(this).parents('tr') ).data().id_casteller;
                let url = '{{ route('castellers.edit', ['casteller' => ':id_casteller']) }}';
                url = url.replace(':id_casteller', id_casteller);
                window.location.href = url;
            } );

            // Event when the filter fields change
            $('#tags, #status, #filter_search_type').change(function(){
                $("#castellers").DataTable().page(0);
                $("#castellers").DataTable().state.save();
                var selectedTags = $('#tags').val();
                sessionStorage.setItem('casteller_tags', JSON.stringify(selectedTags));
                sessionStorage.setItem('casteller_status', $('#status').val());
                sessionStorage.setItem('casteller_filter_search_type', $('#filter_search_type').val());

                $("#castellers").DataTable().ajax.reload(null, false );
            });

            // Configure tag selectize
            $('.selectize2, .tags_add').select2({language: "ca"});

            // Configure filter_search_type selectize
            $("#filter_search_type").select2({
                minimumResultsForSearch: -1,
                language: "ca"
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Configure add new Casteller button
            $('.btn-responsive-text').first().on('click', function (event)
            {
                $('#modalAddCasteller').modal('show');
            });

            // Configure import Castellers from excel button
            $('.btn-responsive-text').eq(1).on('click', function (event)
            {
                $('#modalAddCastellerExcel').modal('show');
            });

            // Configure reset filters button
            $('#resetFilter').on('click', function (event)
            {
                resetFilters();
            });

            // Configure export button events
            $('.btn-export-castellers').on('click', function (event)
            {
                event.preventDefault();
                window.location.href = "{{ route('castellers.export') }}";
            });

            $('.btn-export-castellersods').on('click', function (event)
            {
                event.preventDefault();
                window.location.href = "{{ route('castellers.exportods') }}";
            });

        });
    </script>
@endsection
