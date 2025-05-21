
<div class="block block-themed block-transparent mb-0">

    <div class="block-header bg-primary-dark">
        <h3 class="block-title">
            {!! trans('notifications.send_reminder') !!}
        </h3>
    </div>
    <div class="block-content">
        <div class="block-content">
            <div class="row form-group">
                <div class="col-md-12">
                    <span>{!! trans('notifications.confirm_reminder_notification') !!}</span>
                    <div class="pb-10">
                        {!! \App\Helpers\RenderHelper::fieldTextarea('',10,5,false,false,'message','message','message','message') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-alt-primary btn-send-reminder" data-dismiss="modal" data-id_event="">{!! trans('notifications.send') !!}</button>
        <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
    </div>
</div>
