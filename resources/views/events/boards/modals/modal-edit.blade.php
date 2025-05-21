
<div class="block block-themed block-transparent mb-0">

    <div class="block-header bg-primary-dark">
        <h3 class="block-title">
            {!! trans('boards.edit_pinya') !!}
        </h3>
    </div>
    {!! Form::open(array('id' => 'FormEditBoard', 'url' => route('event.board.edit-board-event', ['boardEvent' => $boardEvent->getId()]), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
    {!! \App\Helpers\RenderHelper::fieldInput(isset($fromRondes)?:0,'hidden',false,false,'fromRondes','fromRondes','fromRondes','fromRondes') !!}
    <div class="block-content">
        <div class="block-content">
            <div class="row form-group">
                <div class="col-md-12">
                    <label>{!! trans('event.add_first_board_txt_name') !!}</label>
                    <div class="pb-10">
                        {!! \App\Helpers\RenderHelper::fieldInput(($boardEvent->getName())?:'','text',false,false,'name','name','name','name') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" form="FormEditBoard" class="btn btn-alt-primary"><i class="fa fa-plus-circle mr-5"></i>{!! trans('general.edit') !!}</button>
        <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
    </div>
    {!! Form::close() !!}
</div>
