@extends('template.main')

@section('title', 'Periodes - Llistat de Periodes')
@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
@endsection
@section('css_after')
@endsection

@section('content')

<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title"><b>{!! trans('period.periods') !!}</b></h3>
    </div>
    <div class="block-content block-content-full">
        <div class="row">
            <div class="col-md-12 text-right" style="margin-bottom: 20px;">
                <button class="btn btn-primary btn-add-period"><i class="fa fa-plus-circle"></i> {!! trans('period.add_period') !!}</button>
            </div>

            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="periods">
                        <thead>
                        <tr>
                            <th>{!! trans('general.name') !!}</th>
                            <th>{!! trans('period.start_period') !!}</th>
                            <th>{!! trans('period.end_period') !!}</th>
                            <th>#</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($periods as $period)
                            <tr>
                                <td>{!! $period->getName() !!}</td>
                                <td>{!! $period->getStartPeriod() !!} </td>
                                <td>{!! $period->getEndPeriod() !!}</td>
                                <td>
                                    @if($period->getId())<button class="btn btn-warning btn-edit-period" data-id_period="{!! $period->getId() !!}"><i class="fa fa-pencil"></i></button>@endif
                                    <button class="btn btn-danger btn-delete-period" data-id_period="{!! $period->id_period !!}"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- START - Modal Update Period -->
<div class="modal fade" id="modalUpdatePeriod" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <div class="modal-content" id="modalUpdatePeriodContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>
<!-- END - Modal Update Period -->

<!-- START - Modal Add Period -->
<div class="modal fade" id="modalAddPeriod" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <div class="modal-content" id="modalAddPeriodContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>
<!-- END - Modal Add Period -->

<!-- START - MODAL DELETE -->
<div class="modal fade" id="modalDelPeriod" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-popin" role="document">
        <div class="modal-content">
            {!! Form::open(array('url' => 'n', 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formDelPeriod')) !!}
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">{!! trans('period.del_period') !!}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>

                <div class="block-content text-center">
                    <i class="fa fa-warning" style="font-size: 46px;"></i>
                    <h3 class="semibold modal-title text-danger">{!! trans('general.caution') !!}</h3>
                    <p class="text-muted">{!! trans('period.del_period_warning') !!}</p>
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
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>


    <script>
        $(function ()
        {

            $("#periods").DataTable({
                "language": {!! trans('datatables.translation') !!},
                "stateSave": true,
                "stateDuration": -1
            });

            $('.btn-add-period').on('click', function (event)
            {
                $('#modalAddPeriod').modal('show');

                $('#modalAddPeriodContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                $.get( "{{ route('events.periods.add-period-modal') }}", function( data ) {
                    $('#modalAddPeriodContent').html( data );
                });
            });

            $('#periods').on('click','.btn-delete-period', function()
            {
                var id_period = $(this).data().id_period;

                var url = "{{ route('events.periods.destroy', ':id_period') }}";
                url = url.replace(':id_period', id_period)

                $('#formDelPeriod').attr('action', url);
                $('#modalDelPeriod').modal('show');
            });


           $("#periods").on('click', '.btn-edit-period', function (event)
            {
                var id_period = $(this).data().id_period;

                $('#modalAddPeriod').modal('show');

                $('#modalAddPeriodContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');


               var url = "{{ route('events.periods.edit-period-modal', ':id_period') }}";
                url = url.replace(':id_period',id_period);
                console.log(url);
                $.get( url, function( data ) {
                    $('#modalAddPeriodContent').html( data );
                });

            });


            $('body').on('focus',"input.datepicker", function(){
                $(this).datepicker({
                    @if (Auth()->user()->language=='ca')
                    language: 'ca',
                    @elseif(Auth()->user()->language=='es')
                    language: 'es',
                    @endif
                    format: "dd/mm/yyyy",
                    autoclose: true,
                });
            });

            $("#start_period").on('changeDate', function(event){
                $("input[name='start_period']").val($('#start_period').datepicker('getDates'));
            });

            $("#end_period").on('changeDate', function(event){
                $("input[name='end_period']").val($('#end_period').datepicker('getDates'));
            });

        });
    </script>
@endsection
