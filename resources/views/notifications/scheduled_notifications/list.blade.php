@extends('template.main')

@section('title', trans('notifications.notifications'))
@section('css_before')
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
            <p>
        </div>
        @can('edit notifications')
            <div class="block-options">
                <a href="{{  route('notifications.scheduled_notifications.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-file-text"></i> {!! trans('notifications.add_notifications') !!}</a>
            </div>
        @endcan
    </div>

<x-data-table :datatable="$datatable"/>

<!-- The Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-popin"  role="document">
        <div class="modal-content">
            {!! Form::open(array('url' => '', 'method' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'formDelNotification')) !!}
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">{!! trans('notifications.del_notification') !!}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>

                <div class="block-content text-center">
                    <i class="fa fa-warning" style="font-size: 46px;"></i>
                    <h3 class="semibold modal-title text-danger">{!! trans('general.caution') !!}</h3>
                    <p class="text-muted">{!! trans('notifications.del_notification_warning') !!}</p>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::submit(trans('general.delete') , array('class' => 'btn btn-danger')) !!}
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>


@endsection

@section('js')
    @stack('datatables_js')
    <script>
        $(function ()
        {
            $("#notifications").on('click', '.btn-delete-notification', function (e)
            {
                e.preventDefault();

                var id_scheduled_notification = $(this).data().id_scheduled_notification;

                $('#deleteModal').modal('show');

                var action="{{ route('notifications.scheduled_notifications.destroy', ['scheduledNotification' => ':id_scheduled_notification']) }}";
                action = action.replace(':id_scheduled_notification', id_scheduled_notification);

                $('#formDelNotification').attr('action', action);
            });
        } );
    </script>
@endsection
