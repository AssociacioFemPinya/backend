
<div class="block block-themed block-transparent mb-0">
    <div class="block-header bg-primary-dark">
        <h3 class="block-title text-left">
            {!! trans('casteller.send_credentials_mail_preview_message') !!}
        </h3>
        <div class="block-options">
                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                    <i class="si si-close"></i>
                </button>
        </div>
    </div>

    <div class="block-content">
        <div class="block-content">
            <div class="row form-group">
                <div class="col-md-12">
                {!! $email_view !!}
                </div>
            </div>
        </div>
    </div>
</div>
