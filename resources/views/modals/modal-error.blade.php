<div class="modal fade" id="modalError" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-popin"  role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">
                        {!! trans('general.error') !!}
                    </h3>
                </div>
                <div class="block-content">
                    <div class="row">
                        <div class="block-content text-center">
                            <i class="fa-solid fa-triangle-exclamation text-danger  pb-20" style="font-size: 46px;"></i>
                            <p class="text-muted">{!! trans('general.error_message') !!}</p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
                </div>
            </div>
        </div>
    </div>
</div>

