@extends('members.template.main')

@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
@endsection
@section('content')

<x-data-table :datatable="$datatable"/>

<!-- START - Modal long -->
<div class="modal fade" id="modalNotificationInfo" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-popin" role="document">
        <div class="modal-content" id="modalNotificationInfoContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>

@endsection

@section('js')
    @stack('datatables_js')
    <script>
        $(function ()
        {
            $(document).on('click', '.btn-info', function (event) {
                const notificationId = $(event.currentTarget).data("id_notification");
                const url = `{{ route('member.get.notification-info-modal', ':notificationId') }}`.replace(':notificationId', notificationId);

                $.get(url)
                    .done(function(data) {
                        $('#modalNotificationInfoContent').html(data);
                        $('#modalNotificationInfo').modal('show');
                    })
                    .fail(function(xhr) {
                        alert(xhr.responseJSON.message);
                    });
            });
        } );
    </script>
@endsection
