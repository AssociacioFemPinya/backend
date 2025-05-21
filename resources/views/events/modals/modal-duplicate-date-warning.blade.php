<!-- START - Modal Duplicate Date Warning -->
<div class="modal fade" id="modalDuplicateDateWarning" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="modalDuplicateDateWarningContent">
            <!-- MODAL CONTENT -->
            <div class="block block-themed block-transparent mb-0" id="profile">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">{!! trans('event.duplicate_date_warning') !!}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content text-center">
                    <i class="fa fa-warning" style="font-size: 46px;"></i>
                    <h3 class="semibold modal-title text-danger">{!! trans('general.caution') !!}</h3>
                    <p class="text-muted">{!! trans('event.duplicate_date_warning_message') !!}</p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END - Modal Duplicate Date Warning -->