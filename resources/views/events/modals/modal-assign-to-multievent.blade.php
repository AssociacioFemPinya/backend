<!-- START - Modal Assign to Multievent -->
<div class="modal fade" id="modalAssignToMultievent" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="modalAssignToMultieventContent">
            <!-- MODAL CONTENT -->
            <div class="block block-themed block-transparent mb-0" id="profile">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">{!! trans('event.assign_to_multievent') !!}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>

                {!! Form::open(array('id' => 'formAssignToMultievent', 'url' => route('events.assign-to-multievent'), 'method' => 'POST', 'class' => '')) !!}
                
                <input type="hidden" name="selected_events" id="selected_events_input">

                <div class="block-content">
                    <div class="row">
                        <div class="col-md-12 mb-20">
                            <p>{!! trans('event.selected_events_count') !!}: <span id="selected_events_count">0</span></p>
                        </div>
                    </div>

                    <div class="row mb-20">
                        <div class="col-md-12">
                            <div class="custom-control custom-radio custom-control-inline mb-5">
                                <input class="custom-control-input" type="radio" name="multievent_option" id="multievent_option_new" value="new" checked>
                                <label class="custom-control-label" for="multievent_option_new">{!! trans('event.create_new_multievent') !!}</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline mb-5">
                                <input class="custom-control-input" type="radio" name="multievent_option" id="multievent_option_existing" value="existing">
                                <label class="custom-control-label" for="multievent_option_existing">{!! trans('event.use_existing_multievent') !!}</label>
                            </div>
                        </div>
                    </div>

                    <div id="new_multievent_options">
                        <div class="row">
                            <div class="col-md-12 mb-10">
                                <label>{!! trans('general.name') !!} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="multievent_name" id="multievent_name">
                                <small class="form-text text-muted">{!! trans('event.new_multievent_attributes_note') !!}</small>
                            </div>
                        </div>
                    </div>

                    <div id="existing_multievent_options" style="display:none;">
                        <div class="row">
                            <div class="col-md-12 mb-10">
                                <label>{!! trans('event.select_existing_multievent') !!} <span class="text-danger">*</span></label>
                                <select class="form-control" name="existing_multievent_id" id="existing_multievent_id">
                                    <option value="">{!! trans('event.select_multievent') !!}</option>
                                    @foreach($multievents as $multievent)
                                        <option value="{{ $multievent->getId() }}">{{ $multievent->getName() }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">{!! trans('event.existing_multievent_note') !!}</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" form="formAssignToMultievent" id="btnAssignToMultievent" class="btn btn-alt-primary">{!! trans('general.ok') !!}</button>
                    <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
                </div>
                
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<!-- END - Modal Assign to Multievent -->