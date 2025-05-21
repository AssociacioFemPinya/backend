
<div class="block block-themed block-transparent mb-0">

    <div class="block-header bg-primary-dark">
        <h3 class="block-title">
            {!! trans('boards.remove_missing_castellers') !!}
        </h3>
        <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                <i class="si si-close"></i>
            </button>
        </div>
    </div>
    <div class="block-content">
        <div class="block-content">
            <div class="row form-group">
                <div class="col-md-12">
                    <label class="control-label">{!! trans('boards.select_attendance_type') !!}</label>
                    <select name="attendanceType" id="attendanceTypeEvent" class="form-control" required>
                        <option value="status" >{!! trans('attendance.attendance_status') !!}</option>
                        <option value="status_verified">{!! trans('attendance.attendance_status_verified') !!}</option>
                    </select>
                </div>
                <div class="col-md-12 pt-10">
                    <label class="control-label">{!! trans('boards.select_attendance_status') !!}</label>
                    <select name="attendanceStatus" id="attendanceStatusEvent" class="form-control" required>
                        <option value="onlyNo" >{!! trans('boards.option_remove_only_no') !!}</option>
                        <option value="allButYes">{!! trans('boards.option_remove_all_but_yes') !!}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-alt-primary btn-remove-missing-form" data-dismiss="modal"><i class="fa-solid fa-user-slash mr-5"></i>{!! trans('boards.remove_missing') !!}</button>
        <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
    </div>
</div>



