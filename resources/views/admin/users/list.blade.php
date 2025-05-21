@extends('template.main')

@section('title', 'Administració - Llistat de colles')
@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{!! asset('js/plugins/cloudflare-switchery/css/switchery.min.css') !!}">

@endsection
@section('css_after')
@endsection

@section('content')

<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title"><b>{!! trans('general.users') !!}</b></h3>
    </div>
    <div class="block-content block-content-full">
        <div class="row">

            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="users">
                        <thead>
                        <tr>
                            <th>{!! trans('general.name') !!}</th>
                            <th>{!! trans('general.email') !!}</th>
                            <th>{!! trans('general.colla') !!}</th>
                            <th>{!! trans('user.role') !!}</th>
                            <th>{!! trans('user.permissions') !!}</th>
                            <th>{!! trans('admin.last_login') !!}</th>
                            <th width="5%">#</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{!! $user->getName() !!}</td>
                                <td>{!! $user->getEmail() !!}</td>
                                <td>{!! $user->getColla()->getShortName() !!}</td>
                                <td>{{ implode(', ',$user->getRoleNames()->toArray()) }}</td>
                                <td>
                                    @include('profile.partials.privileges')
                                </td>
                                <td>
                                    @if(is_null($user->getLastLogin()))
                                        {!! trans('user.not_logging') !!}
                                    @else
                                        {!! $user->getLastLogin() !!}
                                    @endif
                                </td>

                                <td>
                                    <button class="btn btn-sm btn-warning btn-update-user" data-id_user="{!! $user->id_user !!}"><i class="fa fa-pencil"></i></button>
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

<!-- START - Modal Update User -->
<div class="modal fade" id="modalUpdateUser" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
        <div class="modal-content" id="modalUpdateUserContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>
<!-- END - Modal Update User -->

@endsection

@section('js')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/cloudflare-switchery/js/switchery.js') }}"></script>


    <!-- Page JS Code -->
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>

    <script>
        $(function ()
        {
            $("#users").DataTable({
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

            $("#users").on('click', '.btn-update-user', function (event)
            {
                var id_user = $(this).data().id_user;

                $('#modalUpdateUser').modal('show');

                $('#modalUpdateUserContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                var url = "{{ route('profile.colla.edit-user-modal', ':id_user') }}";
                url = url.replace(':id_user',id_user);

                $.get( url, function( data ) {
                    $('#modalUpdateUserContent').html( data );
                });

            });

            let elems = Array.prototype.slice.call(document.querySelectorAll('.js-switchery'));
            elems.forEach(function (html) {
                new Switchery(html, {size: 'small'});
            });

        });
    </script>
@endsection
