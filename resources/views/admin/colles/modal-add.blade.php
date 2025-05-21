<style>
    .custom-file-input ~ .custom-file-label::after {
        content: "{!! trans('admin.btn_input_file_text') !!}";
    }
</style>
<div class="block block-themed block-transparent mb-0">
    <div class="block-header bg-primary-dark">
        <h3 class="block-title">
            @if(isset($colla))
                {!! trans('admin.update_colla') !!}
            @else
                {!! trans('admin.add_colla') !!}
            @endif
        </h3>
        <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                <i class="si si-close"></i>
            </button>
        </div>
    </div>
    @if(isset($colla))
        {!! Form::open(array('id' => 'FormUpdateColla', 'url' => route('admin.colles.update', $colla->getId()), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
    @else
        {!! Form::open(array('id' => 'FormAddColla', 'url' => route('admin.colles.add'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
    @endif
    <div class="block-content">
        <div class="row form-group">
            <div class="col-md-9">
                <label class="control-label">{!! trans('admin.name_colla') !!}</label>
                <input type="text" class="form-control" id="name" name="name" value="@if(isset($colla)){!! $colla->getName() !!}@endif" required>
            </div>
            <div class="col-md-3">
                <label class="control-label">{!! trans('admin.shortname') !!}</label>
                <input type="text" class="form-control" id="shortname" name="shortname" value="@if(isset($colla)){!! $colla->getShortName() !!}@endif" required @if(isset($colla)) disabled  @endif>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-7">
                <label class="control-label">{!! trans('general.email') !!}</label>
                <input type="email" class="form-control" id="email" name="email" value="@if(isset($colla)){!! $colla->getEmail() !!}@endif" required>
            </div>
            <div class="col-md-5">
                <label class="control-label">{!! trans('general.phone') !!}</label>
                <input type="number" class="form-control" id="phone" name="phone" value="@if(isset($colla)){!! $colla->getPhone() !!}@endif" required>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-6">
                <label class="control-label">{!! trans('general.country') !!}</label>
                <input type="text" class="form-control" id="country" name="country" value="@if(isset($colla)){!! $colla->getCountry() !!}@endif" required>
            </div>
            <div class="col-md-6">
                <label class="control-label">{!! trans('general.city') !!}</label>
                <input type="text" class="form-control" id="city" name="city" value="@if(isset($colla)){!! $colla->getCity() !!}@endif" required>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-10">

                <label class="control-label">{!! trans('general.logo') !!}</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="logo" id="logo" accept="image/png, image/jpeg">
                    <label class="custom-file-label" for="logo">{!! trans('general.select_file') !!}</label>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-6">
                <label class="control-label">{!! trans('admin.max_members') !!}</label>
                <input type="text" class="form-control" id="max_members" name="max_members" value="@if(isset($colla)){!! $colla->getMaxMembers() !!}@endif" required>
            </div>
            <div class="col-md-6">
            </div>
        </div>
          {{--  Desactivat fins aprovacio al grup  --}}
        {{--  <div class="row form-group">
            <div class="col-md-10">

                <label class="control-label">{!! trans('general.banner') !!}</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="banner" id="banner" accept="image/png, image/jpeg">
                    <label class="custom-file-label" for="banner">{!! trans('general.select_file') !!}</label>
                </div>
            </div>
            <div class="col-md-2">
                <label class="control-label">{!! trans('general.color') !!}</label>
                <input type="color" class="form-control" id="color" name="color" value="@if(isset($colla)){!! $colla->color !!}@endif" required>
            </div>
        </div>  --}}

    </div>
</div>
<div class="modal-footer">
    @if(isset($colla))
        <button type="submit" form="FormUpdateColla" class="btn btn-alt-primary">{!! trans('general.update') !!}</button>
    @else
        <button type="submit" form="FormAddColla" class="btn btn-alt-primary">{!! trans('general.save') !!}</button>
    @endif
    <button type="button" class="btn  btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
</div>
{!! Form::close() !!}

<script>
    $(function(){
        $('#logo').on('change',function(){
            //get the file name
            var fieldVal = $(this).val();

            // Change the node's value by removing the fake path (Chrome)
            fieldVal = fieldVal.replace("C:\\fakepath\\", "");

            //replace the "Choose a file" label
            $(this).next('.custom-file-label').html(fieldVal);
        });
        $('#banner').on('change',function(){
            //get the file name
            var fieldVal = $(this).val();

            // Change the node's value by removing the fake path (Chrome)
            fieldVal = fieldVal.replace("C:\\fakepath\\", "");

            //replace the "Choose a file" label
            $(this).next('.custom-file-label').html(fieldVal);
        });

    });
</script>
