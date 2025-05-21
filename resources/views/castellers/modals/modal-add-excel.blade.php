<div class="block block-themed block-transparent mb-0" id="profile">
    <div class="block-header bg-primary-dark">
        <h3 class="block-title">{!! trans('casteller.add_casteller_excel') !!}</h3>
        <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                <i class="si si-close"></i>
            </button>
        </div>
    </div>

<form action="{{ route('uploadcastellers') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="col-lg-12 py-3">
        <input type="file" class="form-control" style="padding: 3px;" name="users" accept=".xlsx,.xls,.ods" required />
    </div>

<div class="modal-footer">
        <!-- <button type="submit" class="btn btn-success" name="upload">Upload</button> -->
        @can('edit BBDD')
            <button type="submit" class="btn btn-alt-primary" name="upload"><i class="fa fa-save"></i> {!! trans('general.save') !!}</button>
        @endif
</div>

</form>
