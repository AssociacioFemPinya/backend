    <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary-dark">
        <h3 class="block-title">
            {!! trans('user.add_user') !!}
        </h3>
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                <i class="si si-close"></i>
            </button>
        </div>
    </div>
{!! Form::open(array('id' => 'FormAddUserPermissions', 'url' => route('profile.colla.add-user'), 'method' => 'POST', 'class' => '')) !!}
    <div class="block-options">
        <div class="block-content">
            <div class="row form-group">
                <div class="col-md-2">
                    <label class="control-label">{!! trans('general.name') !!}</label>
                    <input type="text" class="form-control" id="name" name="name" value="{!! old('name') !!}" required>
                </div>
                <div class="col-md-2">
                    <label class="control-label">{!! trans('general.email') !!}</label>
                    <input type="email" class="form-control" id="email" name="email" value="{!! old('email') !!}" required>
                </div>
                <div class="col-md-2">
                    <label class="control-label">{!! trans('user.password') !!}</label>
                    <input type="password" class="form-control" id="password" name="password" value="" required>
                </div>
                <div class="col-md-2">
                    <label class="control-label">{!! trans('user.confirm_password') !!}</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" value="" required>
                </div>
                <div class="col-md-2">
                    <label class="control-label">{!! trans('user.role') !!}</label>
                    <select name="role" id="role" class="form-control" >
                        @foreach ($roles as $id => $role)
                                <option class="text-warning" value="{{ $role['id'] }}">{{ $role['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="profile-settings-email red">{!! trans('user.choose_language') !!}</label>
                    <select name="language" id="language" class="form-control">
                        @foreach(App\Enums\Lang::getTypes() as $lang)
                            <option value={!! strtolower(App\Enums\Lang::getById($lang))!!}>{!! trans()->get('general.languages')[$lang] !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-12">
                    @include('profile.partials.privileges-edit')
                </div>
            </div>
    </div>
    <div class="modal-footer">
        <button type="submit" form="FormAddUserPermissions" class="btn btn-alt-primary">{!! trans('general.save') !!}</button>
        <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
    </div>
{!! Form::close() !!}
</div>

