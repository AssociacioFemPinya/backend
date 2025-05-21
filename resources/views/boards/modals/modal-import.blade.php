
<div class="block block-themed block-transparent mb-0">

    <div class="block-header bg-primary-dark">
        <h3 class="block-title">
        {!! trans('boards.select_base_import') !!}
        </h3>
        <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                <i class="si si-close"></i>
            </button>
        </div>
    </div>
    {!! Form::open(array('id' => 'FormImportBoard', 'url' => route('boards.import'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
    <div class="block-content">
        <div class="block-content">
            @if(count($bases) == 0)
                <div class="danger-text">
                    {!! trans('boards.create_base_warn') !!}
                </div>
            @endif
            @if(count($posicions) < 5)
                <div class="danger-text">
                    {!! trans('boards.create_position_warn') !!}  
                </div>
            @endif
            <div class="row form-group">
                <input type="hidden" name="pinya_id" id="pinya_id" ></input>
                <div class="col-md-4">
                    <label class="control-label">{!! trans('boards.base') !!}</label>
                    <select name="base_id" id="base_id" class="form-control" required>
                        @foreach ($bases as $key => $base)
                        <option class="" value="{!! $base->id_tag !!}">{!! $base->name !!}</option>
                        @endforeach    
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        @if(count($bases) == 0 || count($posicions) == 0)
        <button type="submit" form="FormImportBoard" class="btn btn-alt-primary" disabled>{!! trans('boards.create_pos_danger') !!}</button>
        @else
        <button type="submit" form="FormImportBoard" class="btn btn-alt-primary">{!! trans('boards.import_pinya') !!}</button>
        @endif
        <a type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</a>
    </div>
    {!! Form::close() !!}
    
</div>


