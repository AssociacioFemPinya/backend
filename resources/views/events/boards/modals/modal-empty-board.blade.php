
<div class="block block-themed block-transparent mb-0">
    <div class="block-header bg-primary-dark">
        <h3 class="block-title">
            {!! trans('boards.empty_board') !!}
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
        <p class="text-muted">{!! trans('boards.empty_board_warning') !!}</p>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn btn-danger btn-empty-board-form" data-dismiss="modal"><i class="fa-regular fa-user mr-5"></i>{!! trans('general.delete') !!}</button>
        <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
    </div>
</div>



