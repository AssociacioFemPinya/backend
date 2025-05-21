
<div class="block block-themed block-transparent mb-0">

    <div class="block-header bg-primary-dark">
        <h3 class="block-title">
            {!! trans('boards.add_template') !!}
        </h3>
        <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                <i class="si si-close"></i>
            </button>
        </div>
    </div>
    {!! Form::open(array('id' => 'FormAddBoard', 'url' => route('boards.add'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
    <div class="block-content">
        <input type="hidden" name="type_map" id="type_map" value="PINYA">
        <div class="block-content">
            <div class="row form-group">
                <div class="col-md-4">
                    <label class="control-label">{!! trans('boards.type') !!}</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="" selected disabled>{!! trans('boards.select_type') !!}</option>
                        <option value="PINYA">{!! trans('boards.pinya') !!}</option>
                        <option value="FOLRE">{!! trans('boards.folre') !!}</option>
                        <option value="MANILLES">{!! trans('boards.manilles') !!}</option>
                        <option value="PUNTALS">{!! trans('boards.puntals') !!}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="control-label">{!! trans('boards.base') !!}</label>
                    <select name="base" id="base" class="form-control" required>
                        <option value="" selected disabled>{!! trans('boards.select_structure') !!}</option>
                        @foreach($bases as $base)
                            <option value="{!! $base->getId() !!}">{!! $base->getName() !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="control-label">{!! trans('general.name') !!}</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" form="FormAddBoard" class="btn btn-alt-primary">{!! trans('general.save') !!}</button>
        <a type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</a>
    </div>
    {!! Form::close() !!}
</div>
