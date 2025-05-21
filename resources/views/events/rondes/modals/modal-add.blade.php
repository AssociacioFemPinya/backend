<div class="block block-themed block-transparent mb-0" id="profile">
    <div class="block-header bg-primary-dark">
        <h3 class="block-title">{!! trans('rondes.add_ronda') !!}</h3>
        <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                <i class="si si-close"></i>
            </button>
        </div>
    </div>

    {!! Form::open(array('id' => 'FormAddRonda', 'url' => route('rondes.add'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}

    <ul class="nav nav-tabs nav-tabs-alt" data-toggle="tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#btabs-static-home">
                <i class="fa fa-address-book-o"></i> {!! trans('casteller.card_basic') !!}
            </a>
        </li>
    </ul>
    <div class="block-content tab-content">
        <!-- #1 -->
        <div class="tab-pane active" id="btabs-static-home" role="tabpanel">

            <div class="row form-group">
                <div class="col-md-7">
                    <label class="control-label">{!! trans('casteller.alias') !!} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="alias" name="alias" value="" required>
                </div>
                <div class="col-md-5">
                    <label class="control-label">{!! trans('casteller.status') !!} <span class="text-danger">*</span></label>
                    <select class="form-control" name="status" id="status" required>
                        @foreach ($statuses as $num => $status)
                            @if ($num === 1)
                                <option value="{{ $num }}" selected>{{ $status }}</option>
                            @else
                                <option value="{{ $num }}">{{ $status }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>


    </div>
<!-- END #1 -->
</div>

</div>
<div class="modal-footer">
    @can('edit BBDD')
        <button type="submit" form="FormAddCasteller" class="btn btn-alt-primary"><i class="fa fa-save"></i> {!! trans('general.save') !!}</button>
    @endif
    <button type="button" class="btn  btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
</div>
{!! Form::close() !!}
