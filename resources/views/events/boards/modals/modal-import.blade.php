
<div class="block block-themed block-transparent mb-0">

    <div class="block-header bg-primary-dark">
        <h3 class="block-title">
            {!! trans('boards.import') !!}
        </h3>
        <div class="block-options">
            @if($event->getBoards()->isEmpty())
                <button type="button" class="btn-block-option disabled" disabled>
                    <i class="si si-close"></i>
                </button>
            @else
                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                    <i class="si si-close"></i>
                </button>
            @endif
        </div>
    </div>
    {!! Form::open(array('id' => 'FormImportBoard', 'url' => route('event.board.import-board-event', $boardEvent->getId()), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
    <div class="block-content">
        <div class="block-content">
            <div class="row form-group">
                <div class="col-md-12">
                    <label class="control-label">{!! trans('event.select_board_event') !!}</label>
                    <select name="importBoardEvent" id="importBoardEvent" class="form-control" required>
                        <option value="" disabled>{!! trans('boards.favourites') !!}</option>
                        @foreach($boardFavourites as $favourite)
                            <option value="{{ $favourite->getId() }}">{!! $favourite->getEvent()->getStartDate()->format('Y/m/d'). ' | '. $favourite->getDisplayName() . ' | '.$favourite->getEvent()->getName()  !!} </option>
                        @endforeach
                        <option value="" disabled>{!! trans('boards.rest') !!}</option>
                        @foreach($boardNotFavourites as $favourite)
                            <option value="{{ $favourite->getId() }}">{!! $favourite->getEvent()->getStartDate()->format('Y/m/d'). ' | '. $favourite->getDisplayName() . ' | '.$favourite->getEvent()->getName()  !!} </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" form="FormImportBoard" class="btn btn-alt-primary"><i class="fa fa-cloud-upload mr-5"></i>{!! trans('general.import') !!}</button>
        @if(empty($boardFavourites) && empty($boardNotFavourites))
            <button type="button" class="btn btn-alt-secondary disabled" disabled>{!! trans('general.close') !!}</button>
        @else
            <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
        @endif
    </div>
    {!! Form::close() !!}
</div>
