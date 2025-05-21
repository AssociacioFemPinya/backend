
<div class="block block-themed block-transparent mb-0">

    <div class="block-header bg-primary-dark">
        <h3 class="block-title">
            {!! trans('event.add_pinya') !!}
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
    {!! Form::open(array('id' => 'FormAttachBoard', 'url' => route('event.board.attach', $event->getId()), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
    <div class="block-content">
        <div class="block-content">
            <div class="row form-group">
                <div class="col-md-12">
                    <label>{!! trans('event.add_first_board_txt_name') !!}</label>
                    <div class="pb-10">
                        {!! \App\Helpers\RenderHelper::fieldInput('','text',false,false,'name','name','name','name') !!}
                    </div>
                    <label class="control-label">{!! trans('event.select_board') !!}</label>
                    <select name="board" id="board" class="form-control" required>
                        @foreach($boardsColla as $board)
                            <option value="{{ $board->getId() }}">{!! $board->getName() . ' | '. trans('boards.'.strtolower($board->getType())) !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" form="FormAttachBoard" class="btn btn-alt-primary"><i class="fa fa-plus-circle mr-5"></i>{!! trans('general.add') !!}</button>
        @if($event->getBoards()->isEmpty())
            <button type="button" class="btn btn-alt-secondary disabled" disabled>{!! trans('general.close') !!}</button>
        @else
            <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
        @endif
    </div>
    {!! Form::close() !!}
</div>
