@extends('template.main')

@section('title', trans('user.profile') )
@section('css_after')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <style>
        .custom-file-input ~ .custom-file-label::after {
            content: "{!! trans('admin.btn_input_file_text') !!}";
        }
    </style>
@endsection

@section('content')

<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title">
            <i class="fa fa-user-circle mr-5 text-muted"></i> <b>{!! trans('user.user_profile') !!}</b> {!! $user->getEmail() !!}
        </h3>
    </div>
    <div class="block-content">
        {!! Form::open(array('id' => 'FormUpdateUser', 'url' => route('profile.user.update'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
            <div class="row items-push">
                <div class="col-lg-3">
                    <img src="{{ $user->getProfileImage('med') }}" class="img-avatar img-avatar128" alt="avatar">
                </div>
                <div class="col-lg-7">
                    @role('Super-Admin')
                        <div class="row form-group">
                            <div class="col-12">
                                <label>{!! trans('general.colla') !!}</label>
                                <select class="js-select2 form-control" id="select_colla" name="select_colla">
                                    <option value=""></option><!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                    @foreach($colles as $colla)
                                        <option value="{!! $colla->getId() !!}" @if($colla->getId() === $user->getCollaId()) selected @endif>{!! $colla->getName() !!}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endrole
                    <div class="form-group row">
                        <div class="col-12">
                            <label for="profile-settings-username">{!! trans('user.name') !!}</label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" value="{!! $user->getName() !!}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label for="profile-settings-email">{!! trans('user.choose_language') !!}</label>
                            <select name="language" id="language" class="form-control">
                                @foreach(App\Enums\Lang::getTypes() as $lang)
                                    <option value={!! strtolower(App\Enums\Lang::getById($lang))!!} @if($user->getLanguage() === strtolower(App\Enums\Lang::getById($lang))) selected @endif>{!! trans()->get('general.languages')[$lang] !!}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-10 col-xl-6">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input js-custom-file-input-enabled" id="photo" name="photo" accept="image/png, image/jpeg">
                                <label class="custom-file-label" for="profile-settings-avatar">{!! trans('general.select_file') !!}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <button type="submit" form="FormUpdateUser" class="btn btn-primary">{!! trans('general.update') !!}</button>
                        </div>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>

<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title">
            <i class="fa fa-asterisk mr-5 text-muted"></i> {!! trans('user.change_password') !!}
        </h3>
    </div>
    <div class="block-content">
        {!! Form::open(array('id' => 'ChangePasswordUser', 'url' => route('profile.user.update-password'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
            <div class="row items-push">
                <div class="col-lg-3">
                    <p class="text-muted">
                        {!! trans('user.help_password') !!}
                    </p>
                </div>
                <div class="col-lg-7">
                    <div class="form-group row">
                        <div class="col-12">
                            <label for="profile-settings-password">{!! trans('user.current_password') !!}</label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label for="profile-settings-password-new">{!! trans('user.new_password') !!}</label>
                            <input type="password" class="form-control form-control-lg" id="new_password" name="new_password" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label for="profile-settings-password-new-confirm">{!! trans('user.confirm_new_password') !!}</label>
                            <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <button type="submit" form="ChangePasswordUser" class="btn btn-alt-primary">{!! trans('general.update') !!}</button>
                        </div>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>

@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <script>
        $(function()
        {
            $('.js-select2').select2();

            $('#photo').on('change',function(){
                //get the file name
                var fieldVal = $(this).val();

                // Change the node's value by removing the fake path (Chrome)
                fieldVal = fieldVal.replace("C:\\fakepath\\", "");

                //replace the "Choose a file" label
                $(this).next('.custom-file-label').html(fieldVal);
            });

        });
    </script>
@endsection
