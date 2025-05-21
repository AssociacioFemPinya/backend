@extends('template.main')

@section('title', 'Administració - Llistat de colles')
@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
@endsection
@section('css_after')
@endsection

@section('content')

<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title"><b>{!! trans('admin.colles') !!}</b></h3>
    </div>
    <div class="block-content block-content-full">
        <div class="row">
            <div class="col-md-12 text-right" style="margin-bottom: 20px;">
                <button class="btn btn-primary btn-add-colla"><i class="fa fa-plus-circle"></i> {!! trans('admin.add_colla') !!}</button>
            </div>

            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="colles">
                        <thead>
                        <tr>
                            <th>{!! trans('general.name') !!}</th>
                            <th>{!! trans('admin.shortname') !!}</th>
                            <th>{!! trans('admin.last_login') !!}</th>
                            <th>{!! trans('user.colla_user') !!}</th>
                            <th>{!! trans('admin.num_castellers') !!}/{!! trans('admin.max_members_abreviat') !!} </th>
                            <th>#</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($colles as $colla)
                            <tr>
                                <td>{!! $colla->getName() !!}</td>
                                <td>{!! $colla->getShortname() !!}</td>
                                <td>{!! $colla->getlastLogin()['date'] !!} </td>
                                <td>{!! $colla->getlastLogin()['user'] !!} </td>
                                <td>{!! $colla->numCastellers() !!}/{!! $colla->getMaxMembers() !!}  </td>
                                <td>
                                    <button class="btn btn-warning btn-update-colla" data-id_colla="{!! $colla->id_colla !!}"><i class="fa fa-pencil"></i></button>
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

<!-- START - Modal Update Colla -->
<div class="modal fade" id="modalUpdateColla" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <div class="modal-content" id="modalUpdateCollaContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>
<!-- END - Modal Update Colla -->

<!-- START - Modal Add Colla -->
<div class="modal fade" id="modalAddColla" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <div class="modal-content" id="modalAddCollaContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>
<!-- END - Modal Add Colla -->

<!-- START - MODAL DELETE -->
<div class="modal fade" id="modalDelColla" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-popin" role="document">
        <div class="modal-content">
            {!! Form::open(array('url' => 'n', 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'fromDelColla')) !!}
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">{!! trans('admin.del_colla') !!}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>

                <div class="block-content text-center">
                    <i class="fa fa-warning" style="font-size: 46px;"></i>
                    <h3 class="semibold modal-title text-danger">{!! trans('general.caution') !!}</h3>
                    <p class="text-muted">{!! trans('admin.del_colla_warning') !!}</p>
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

    <!-- Page JS Code -->
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>

    <script>
        $(function ()
        {
            $("#colles").DataTable({
                "language": {!! trans('datatables.translation') !!},
                "stateSave": true,
                "stateDuration": -1
            });

            $('.btn-add-colla').on('click', function (event)
            {
                $('#modalAddColla').modal('show');

                $('#modalAddCollaContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                $.get( "{{ route('admin.colles.add-colla-modal') }}", function( data ) {
                    $('#modalAddCollaContent').html( data );
                });
            });

            $("#colles").on('click', '.btn-update-colla', function (event)
            {
                var id_colla = $(this).data().id_colla;

                $('#modalUpdateColla').modal('show');

                $('#modalUpdateCollaContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                var url = "{{ route('admin.colles.edit-colla-modal', ':id_colla') }}";
                url = url.replace(':id_colla',id_colla);

                $.get( url, function( data ) {
                    $('#modalUpdateCollaContent').html( data );
                });
            });


        });
    </script>
@endsection
