    <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary-dark">
        <h3 class="block-title">
            {!! trans('admin.update_user') !!}
        </h3>

            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                <i class="si si-close"></i>
            </button>
        </div>
    </div>
{!! Form::open(array('id' => 'FormUpdateUserPermissions', 'url' => route('profile.colla.update-user', $user->id_user), 'method' => 'POST', 'class' => '')) !!}
    <div class="block-options">
        <div class="block-content">
            <div class="row form-group">
                <div class="col-md-3">
                    <label class="control-label">{!! trans('general.name') !!}</label>
                    <input type="text" class="form-control" id="name" name="name" value="{!! $user->name !!}" required>
                </div>
                <div class="col-md-3">
                    <label class="control-label">{!! trans('general.email') !!}</label>
                    <input type="email" class="form-control" id="email" name="email" value="{!! $user->email !!}" disabled>
                </div>
                <div class="col-md-3">
                    <label class="control-label">{!! trans('user.role') !!}</label>
                    <select name="role" id="role" class="form-control" >
                        @foreach ($roles as $id => $role)
                            @if (isset($user) && in_array($role['id'],$userRoles))
                                <option class="text-warning" value="{{ $role['id'] }}" selected>{{ $role['name'] }}</option>
                            @else
                                <option class="text-warning" value="{{ $role['id'] }}">{{ $role['name'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="profile-settings-email red">{!! trans('user.choose_language') !!}</label>
                    <select name="language" id="language" class="form-control">
                        @foreach(App\Enums\Lang::getTypes() as $lang)
                            <option value={!! strtolower(App\Enums\Lang::getById($lang))!!} @if($user->getLanguage() === strtolower(App\Enums\Lang::getById($lang))) selected @endif>{!! trans()->get('general.languages')[$lang] !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-12">
                    <label class="control-label">{!! trans('user.permissions') !!}</label>
                    <div>
                        @include('profile.partials.privileges-edit')
                    </div>
                </div>
            </div>
    </div>
    <div class="modal-footer">
        <button type="submit" form="FormUpdateUserPermissions" class="btn btn-alt-primary">{!! trans('general.update') !!}</button>
        <button type="button" class="btn  btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
    </div>
{!! Form::close() !!}
</div>

