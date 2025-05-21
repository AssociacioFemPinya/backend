<!-- START - Modal Attach board -->
<div class="modal fade" id="modalAttachBoard" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" id="modalAttachBoardContent">
            <!-- MODAL CONTENT -->
            <div class="block block-themed block-transparent mb-0" id="profile">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">{!! trans('event.add_pinya') !!}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>

                {!! Form::open(array('id' => 'formAttachBoard', 'url' => '', 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}

                <div class="block-content tab-content">
                    <!-- #1 -->
                    <div class="tab-pane active" id="btabs-static-home" role="tabpanel">

                        <div class="row pb-5">
                            <div class="col-md-12 pb-15">
                                {!! trans('event.add_first_board_txt') !!}
                            </div>
                            <label class="pl-10">{!! trans('event.add_first_board_txt_name') !!}</label>
                            <div class="col-md-12 pb-10">
                                {!! \App\Helpers\RenderHelper::fieldInput('','text',false,false,'name','name','name','name') !!}
                            </div>
                            <label class="pl-10">{!! trans('event.add_first_board_txt_pinya') !!}</label>
                            <div class="col-md-12 pb-10">
                                <select class="form-control" name="board" id="board" required>
                                    <option value="">{!! trans('event.select_board') !!}</option>
                                    @foreach($boardsColla as $board)
                                        <option value="{{ $board->getId() }}">{!! $board->getName() . ' | '. trans('boards.'.strtolower($board->getType())) !!}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- END #1 -->
                </div>

            </div>
            <div class="modal-footer">
                @can('edit events')
                    <button type="submit" form="formAttachBoard" class="btn btn-alt-primary">{!! trans('general.add') !!}</button>
                @endcan
                <button type="button" class="btn  btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
            </div>
            {!! Form::close() !!}

        </div>
    </div>
</div>
<!-- END - Modal Attach board -->
