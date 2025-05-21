<div class="modal-content">
    <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary-dark">
            <h3 class="block-title">{!! trans('boards.base_import_finished') !!}</h3>
            <div class="block-options">
                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                    <i class="si si-close"></i>
                </button>
            </div>
        </div>

        <div class="block-content">
            <p class="text-muted">{!! trans('boards.base_import_finished_txt', ['base' => $base, 'pinya' => $board->getName()]) !!}</p>
            @if(!$board->hasReady())
                <p class="text-muted">{!! trans('boards.bases_not_imported') !!}</p>
            @endif

            @if(!$board->getHtmlPinya())
                <a href="{!! route('boards.add-map', ['board' => $board->getId(), 'map' => \App\Enums\BasesEnum::PINYA]) !!}" class="btn btn-success mb-10">{!! trans('boards.import_pinya') !!}</a><br>
            @endif

            @if(($board->getType() === \App\Enums\BasesEnum::FOLRE || $board->getType() === \App\Enums\BasesEnum::MANILLES || $board->getType() === \App\Enums\BasesEnum::PUNTALS) && !$board->getHtmlFolre())
                <a href="{!! route('boards.add-map', ['board' => $board->getId(), 'map' => \App\Enums\BasesEnum::FOLRE]) !!}" class="btn btn-success mb-10">{!! trans('boards.import_folre') !!}</a><br>
            @endif

            @if(($board->getType() === \App\Enums\BasesEnum::MANILLES || $board->getType() === \App\Enums\BasesEnum::PUNTALS) && !$board->getHtmlManilles())
                <a href="{!! route('boards.add-map', ['board' => $board->getId(), 'map' => \App\Enums\BasesEnum::MANILLES]) !!}" class="btn btn-success mb-10">{!! trans('boards.import_manilles') !!}</a><br>
            @endif

            @if(($board->getType() === \App\Enums\BasesEnum::PUNTALS) && !$board->getHtmlPuntals())
                <a href="{!! route('boards.add-map', ['board' => $board->getType(), 'map' => \App\Enums\BasesEnum::PUNTALS]) !!}" class="btn btn-success mb-10">{!! trans('boards.import_puntals') !!}</a><br>
            @endif

        </div>

        <div class="modal-footer">
            @if(!$board->hasReady())
                <a href="{!! route('boards.list') !!}" class="btn btn-warning mr-auto">{!! trans('boards.not_yet_rtn_boards_list') !!}</a>
            @else
                <a href="{!! route('boards.list') !!}" class="btn btn-primary mr-auto">{!! trans('boards.rtn_boards_list') !!}</a>
            @endif

            <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
        </div>
    </div>
</div><!-- /.modal-content -->
