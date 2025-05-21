<div class="block block-themed block-transparent mb-0">
    <div class="block-header bg-primary-dark">
        <h3 class="block-title">
            {!! trans('event.leave_multievent') !!}
        </h3>
        <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                <i class="si si-close"></i>
            </button>
        </div>
    </div>
    <div class="block-content text-center">
        <i class="fa fa-warning" style="font-size: 46px;"></i>
        <h3 class="semibold modal-title text-danger">{!! trans('general.caution') !!}</h3>
        <p class="text-muted">{!! trans('event.leave_multievent_warning') !!}</p>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-success btn-confirm-leave-multievent" data-dismiss="modal">{!! trans('general.ok') !!}</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancel-leave-multievent">{!! trans('general.nok') !!}</button>
    </div>
</div>