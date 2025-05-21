@extends('template.main')

@section('title', 'Administració - La meva colla')
@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{!! asset('js/plugins/cloudflare-switchery/css/switchery.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
@endsection
@section('css_after')
@endsection

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h2 class="text-center">{!! trans('general.colla') !!}: <span style="font-weight: 100">{!! $colla->getName() !!}</span> </h2>
        <!-- Block Tabs Animated Slide Up -->
        <div class="block">

            <ul class="nav nav-tabs nav-tabs-block" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {!! $active['li_profile'] !!}" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="home" aria-selected="true">{!! trans('general.profile') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {!! $active['li_users'] !!}" id="users-tab" data-toggle="tab" href="#users" role="tab" aria-controls="profile" aria-selected="false">{!! trans('general.users') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {!! $active['li_config'] !!}" id="config-tab" data-toggle="tab" href="#config" role="tab" aria-controls="config" aria-selected="false">{!! trans('general.config') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {!! $active['li_periods'] !!}" id="periods-tab" data-toggle="tab" href="#periods" role="tab" aria-controls="periods" aria-selected="false">{!! trans('period.periods') !!}</a>
                </li>

            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="block-content tab-pane fade {!! $active['div_profile'] !!}" id="profile" role="tabpanel" aria-labelledby="home-tab">
                    <div class="block-header">
                        <div class="block-title">
                            <h3 class="block-title">
                                <b>{!! trans('user.profile_colla') !!}</b>
                            </h3>
                        </div>
                    </div>
                    {!! Form::open(array('id' => 'FormUpdateColla', 'url' => route('profile.colla.update'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
                    <div class="row">
                        <div class="col-lg-3">
                            @if($colla->getLogo())
                                <img src="{{ asset('media/colles/'.$colla->getShortName().'/'.$colla->getLogo()) }}" class="img-fluid" alt="Logo: {!! $colla->getName() !!}">
                            @else
                                <img src="{{ asset('media/avatars/avatar.jpg') }}" class="img-avatar128" alt="Logo: {!! $colla->getName() !!}">
                            @endif

                        </div>
                        <div class="col-lg-9">
                            <div class="row form-group">
                                <div class="col-md-9">
                                    <label class="control-label">{!! trans('admin.name_colla') !!}</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{!! old('name',$colla->getName()) !!}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">{!! trans('admin.shortname') !!}</label>
                                    <input type="text" class="form-control" id="shortname" name="shortname" value="{!! old('shortname',$colla->getShortName()) !!}" required disabled>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-7">
                                    <label class="control-label">{!! trans('general.email') !!}</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{!! old('email',$colla->getEmail()) !!}" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="control-label">{!! trans('general.phone') !!}</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{!! old('phone',$colla->getPhone()) !!}" required>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label class="control-label">{!! trans('general.country') !!}</label>
                                    <input type="text" class="form-control" id="country" name="country" value="{!! old('country',$colla->getCountry()) !!}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label">{!! trans('general.city') !!}</label>
                                    <input type="text" class="form-control" id="city" name="city" value="{!! old('city',$colla->getCity()) !!}" required>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-8">

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
                                    <input type="text" class="form-control" id="" name="max_members" value="{!! old('max_members',$colla->getMaxMembers()) !!}" disabled>
                                </div>
                                <div class="col-md-6">
                                </div>
                            </div>
                            {{--  Desactivat fins aprovacio   --}}
                            {{--  <div class="row form-group">
                                <div class="col-md-8">

                                    <label class="control-label">{!! trans('general.banner') !!}</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="banner" id="banner" accept="image/png, image/jpeg">
                                        <label class="custom-file-label" for="banner">{!! trans('general.select_file') !!}</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label">{!! trans('general.color') !!}</label>
                                    <div class="col-6" style="padding-left: 0;"><input type="color" class="form-control" id="color" name="color" value="{!! $colla->getColor !!}" required=""></div>
                                </div>
                            </div>  --}}

                            <div class="form-group row">
                                <div class="col-12">
                                    @can('edit colla')
                                        <button type="submit" form="FormUpdateColla" class="btn btn-primary">{!! trans('general.update') !!}</button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="block-content tab-pane fade {!! $active['div_users'] !!}" id="users" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="block-header">
                        <div class="block-title">
                            <h3 class="block-title">
                                <b>{!! trans('general.users') !!}</b>
                            </h3>
                        </div>
                        <div class="block-options">
                            @can('edit colla')
                                <button class="btn btn-primary btn-add-user"><i class="fa fa-user-plus"></i> {!! trans('user.add_user') !!}</button>
                            @endcan
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{!! trans('general.name') !!}</th>
                                    <th>{!! trans('user.role') !!}</th>
                                    <th>{!! trans('user.permissions') !!}</th>
                                    <th>{!! trans('admin.last_login') !!}</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{!! $user->getName() !!}</td>
                                        <td>{{ implode(', ',$user->getRoleNames()->toArray()) }}</td>
                                        <td>
                                            @include('profile.partials.privileges')
                                        </td>
                                        <td>
                                            @if(is_null($user->getLastAccessAt()))
                                                {!! trans('user.not_logging') !!}
                                            @else
                                                {!! \App\Helpers\Humans::parseDate($user->last_access_at) !!}
                                            @endif
                                        </td>
                                        <td>
                                            @can('edit colla')
                                                <button class="btn btn-warning btn-update-user" data-id_user="{!! $user->getId() !!}"><i class="fa fa-pencil"></i></button>
                                                <button class="btn btn-danger btn-delete-user" data-id_user="{!! $user->getId() !!}"><i class="fa fa-trash-o"></i></button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="block-content tab-pane fade {!! $active['div_config'] !!}" id="config" role="tabpanel" aria-labelledby="config-tab">
                    <div class="block-header">
                        <div class="block-title">
                            <h3 class="block-title"><b>{!! trans('config.general_config') !!}</b></h3>
                        </div>
                    </div>
                    <div class="block-content">
                        {!! Form::open(array('id' => 'FormUpdateCollaConfig', 'url' => route('profile.colla.update-colla-config'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
                        <div class="row">
                            <div class="col-12 font-weight-bold title ">{!! trans('user.permission_boards') !!}</div>
                            <div class="col-md-4">
                                <label class="control-label mr-5">{!! trans('config.boards_enabled') !!}</label>
                                {!! \App\Helpers\RenderHelper::fieldSwitcher($config->getBoardsEnabled(), 'data-id_colla', $colla->getId(), 'boards_enabled', 'boards_enabled', 'boards_enabled'); !!}
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <label class="control-label mr-5">{!! trans('config.public_display_enabled') !!}</label>
                                {!! \App\Helpers\RenderHelper::fieldSwitcher($config->getPublicDisplayEnabled(), 'data-id_colla', $colla->getId(), 'public_display_enabled', 'public_display_enabled', 'public_display_enabled'); !!}
                                <span id="publicDisplayUrl" class="text-info ml-5">{!! $config->getPublicDisplayUrl() !!}</span>
                            </div>
                        </div>

                        <div class="row mt-10">
                            <div class="col-md-12 font-weight-bold title">{!! trans('config.translations') !!}</div>
                            <div class="col-md-4">
                                <label class="control-label">{!! trans('config.translation_actuacio') !!}</label>
                                <input type="text" class="form-control" id="translation_actuacio" name="translation_actuacio" placeholder="{!! trans('config.translation_actuacio') !!}" value="{!! $config->getTranslationActuacio() !!}">
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">{!! trans('config.translation_assaig') !!}</label>
                                <input type="text" class="form-control" id="translation_assaig" name="translation_assaig" placeholder="{!! trans('config.translation_assaig') !!}" value="{!! $config->getTranslationAssaig() !!}">
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">{!! trans('config.translation_activitat') !!}</label>
                                <input type="text" class="form-control" id="translation_activitat" name="translation_activitat" placeholder="{!! trans('config.translation_activitat') !!}" value="{!! $config->getTranslationActivitat() !!}">
                            </div>
                        </div>


                        <div class="row mt-10">
                            <div class="col-md-12 font-weight-bold title">{!! trans('config.default_language') !!}</div>
                            <div class="col-md-4">
                                <select name="language" id="language" class="form-control">
                                    @foreach(App\Enums\Lang::getTypes() as $lang)
                                        <option value={!! strtolower(App\Enums\Lang::getById($lang)) !!} @if($config->getLanguage() === strtolower(App\Enums\Lang::getById($lang))) selected @endif>{!! trans()->get('general.languages')[$lang] !!}</option>
                                    @endforeach
                                </select>
                                </div>
                        </div>

                        <div class="row mt-10">
                            <div class="col-md-12 font-weight-bold title">{!! trans('config.max_events_telegram') !!}</div>
                            <div class="col-md-4">
                                <label class="control-label">{!! trans('config.translation_actuacio') !!}</label>
                                <input type="number" class="form-control" id="max_actuacions" name="max_actuacions" value="{!! $config->getMaxActuacions() !!}" min="1" max="50">
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">{!! trans('config.translation_assaig') !!}</label>
                                <input type="number" class="form-control" id="max_assaigs" name="max_assaigs" value="{!! $config->getMaxAssaigs() !!}" min="1" max="50">
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">{!! trans('config.translation_activitat') !!}</label>
                                <input type="number" class="form-control" id="max_activitats" name="max_activitats" value="{!! $config->getMaxActivitats() !!}" min="1" max="50">
                            </div>
                        </div>

                        <div class="row mt-10">
                            <div class="col-md-12 font-weight-bold title">{!! trans('config.member_config') !!}</div>
                            <div class="col-md-4">
                                <label class="control-label mr-5">{!! trans('config.member_session_expire') !!}</label>
                                {!! \App\Helpers\RenderHelper::fieldSwitcher($config->getMemberSessionExpire(), 'data-id_colla', $colla->getId(), 'member_session_expire', 'member_session_expire', 'member_session_expire'); !!}
                            </div>
                            <div class="col-md-4">
                                <label class="control-label mr-5">{!! trans('config.members_edit_personal') !!}</label>
                                {!! \App\Helpers\RenderHelper::fieldSwitcher($config->getMemberEditPersonalData(), 'data-id_colla', $colla->getId(), 'member_edit_personal', 'member_edit_personal', 'member_edit_personal'); !!}
                            </div>
                        </div>

                        <div class="row mt-10">
                            <div class="col-md-12 font-weight-bold title">{!! trans('config.google_calendar_telegram') !!}</div>
                            <div class="col-md-4">
                                <label class="control-label mr-5">{!! trans('config.google_calendar_activitats') !!}</label>
                                {!! \App\Helpers\RenderHelper::fieldSwitcher($config->getGoogleCalendarEnabledActivitats(), 'data-id_colla', $colla->getId(), 'google_calendar_activitats', 'google_calendar_activitats', 'google_calendar_activitats'); !!}
                            </div>
                            <div class="col-md-4">
                                <label class="control-label mr-5">{!! trans('config.google_calendar_actuacions') !!}</label>
                                {!! \App\Helpers\RenderHelper::fieldSwitcher($config->getGoogleCalendarEnabledActuacions(), 'data-id_colla', $colla->getId(), 'google_calendar_actuacions', 'google_calendar_actuacions', 'google_calendar_actuacions'); !!}
                            </div>
                            <div class="col-md-4">
                                <label class="control-label mr-5">{!! trans('config.google_calendar_assaigs') !!}</label>
                                {!! \App\Helpers\RenderHelper::fieldSwitcher($config->getGoogleCalendarEnabledAssaigs(), 'data-id_colla', $colla->getId(), 'google_calendar_assaigs', 'google_calendar_assaigs', 'google_calendar_assaigs'); !!}
                            </div>
                        </div>


                        <div class="row mt-10">
                            <div class="col-md-12 font-weight-bold title">{!! trans('config.baseline_values') !!}</div>
                            <div class="col-md-4">
                                <label class="control-label">{!! trans('casteller.height') !!}</label>
                                <input type="number" class="form-control" id="height_baseline" name="height_baseline" value="{!! $config->getHeightBaseline() !!}" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">{!! trans('casteller.shoulder_height') !!}</label>
                                <input type="number" class="form-control" id="shoulder_height_baseline" name="shoulder_height_baseline" value="{!! $config->getShoulderHeightBaseline() !!}" min="0">
                            </div>
                        </div>

                        <div class="row mt-10">
                            <div class="col-md-12 font-weight-bold title">{!! trans('config.totp_token_expiration') !!}</div>
                            <div class="col-md-4">
                                <input type="number" class="form-control" id="totp_token_expiration" name="totp_token_expiration" value="{!! $config->getTOTPTokenExpiration() !!}" min="0">
                            </div>
                        </div>


                        </div>
                        <div class="row mt-10 mb-10">
                            <div class="col-12">
                                @can('edit colla')
                                    <button type="submit" form="FormUpdateCollaConfig" class="btn btn-primary">{!! trans('general.update') !!}</button>
                                @endcan
                            </div>
                        </div>
                        {!! Form::close() !!}
                </div>
                <div class="block-content tab-pane fade {!! $active['div_periods'] !!}" id="periods" role="tabpanel" aria-labelledby="periods-tab">
                        @include('profile.periods.list-block')
                </div>
            </div>
    </div>
        <!-- END Block Tabs Animated Slide Up -->
</div>


<!-- START - Modal Update User -->
<div class="modal fade" id="modalUpdateUser" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
        <div class="modal-content" id="modalUpdateUserContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>
<!-- END - Modal Update User -->


<!-- START - Modal Add User -->
<div class="modal fade" id="modalAddUser" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
        <div class="modal-content" id="modalAddUserContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>
<!-- END - Modal Update User -->


<!-- START - MODAL DELETE -->
<div class="modal fade" id="modalDelUser" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
<div class="modal-dialog modal-sm modal-dialog-popin" role="document">
    <div class="modal-content">
        {!! Form::open(array('url' => 'n', 'method' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'fromDelUser')) !!}
        <div class="block block-themed block-transparent mb-0">
            <div class="block-header bg-primary-dark">
                <h3 class="block-title">{!! trans('admin.del_user') !!}</h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                        <i class="si si-close"></i>
                    </button>
                </div>
            </div>

            <div class="block-content text-center">
                <i class="fa fa-warning" style="font-size: 46px;"></i>
                <h3 class="semibold modal-title text-danger">{!! trans('general.caution') !!}</h3>
                <p class="text-muted">{!! trans('admin.del_user_warning') !!}</p>
            </div>
        </div>
        <div class="modal-footer">
            {!! Form::submit(trans('general.delete') , array('class' => 'btn btn-danger')) !!}
            <button type="button" class="btn  btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
        </div>
        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>
<!--/ END - MODAL DELETE -->

@endsection


@section('js')
    <script src="{{ asset('js/plugins/cloudflare-switchery/js/switchery.js') }}"></script>
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    @if (Auth()->user()->language=='ca')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.ca.min.js') !!}"></script>

        @elseif(Auth()->user()->language=='es')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js') !!}"></script>
    @endif

    <script type="text/javascript">
        $(function () {
            $.ajaxPrefilter(function(options, originalOptions, xhr) { // this will run before each request
                const token = $('meta[name="csrf-token"]').attr('content'); // or _token, whichever you are using

                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token); // adds directly to the XmlHttpRequest Object
                }
            });
        });
    </script>
    <script>
        $(function ()
        {
            $('#logo').on('change',function(){
                //get the file name
                let fieldVal = $(this).val();

                // Change the node's value by removing the fake path (Chrome)
                fieldVal = fieldVal.replace("C:\\fakepath\\", "");

                //replace the "Choose a file" label
                $(this).next('.custom-file-label').html(fieldVal);
            });

            $('#banner').on('change',function(){
                //get the file name
                let fieldVal = $(this).val();

                // Change the node's value by removing the fake path (Chrome)
                fieldVal = fieldVal.replace("C:\\fakepath\\", "");

                //replace the "Choose a file" label
                $(this).next('.custom-file-label').html(fieldVal);
            });

            $(".btn-delete-user").on('click', function (event)
            {
                let id_user = $(this).data().id_user;
                $('#modalDelUser').modal('show');

                let url = "{{ route('profile.colla.delete-user', ':id_user') }}";
                url = url.replace(':id_user',id_user);

                $('#fromDelUser').attr('action', url);
            });

            $(".btn-update-user").on('click', function (event)
            {
                let id_user = $(this).data().id_user;

                $('#modalUpdateUser').modal('show');

                $('#modalUpdateUserContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                let url = "{{ route('profile.colla.edit-user-modal', ':id_user') }}";
                url = url.replace(':id_user',id_user);

                $.get( url, function( data ) {
                    $('#modalUpdateUserContent').html( data );
                });
            });

            $(".btn-add-user").on('click', function (event)
            {
                $('#modalAddUser').modal('show');

                $('#modalAddUserContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                let url = "{{ route('profile.colla.add-user-modal') }}";

                $.get( url, function( data ) {
                    $('#modalAddUserContent').html( data );
                });
            });


            function setStatus(id_colla, status, fieldname)
            {
                $.post( "{{ route('profile.colla.set-status-colla-config') }}",
                    {   'id_colla': id_colla,
                        'status': status,
                        'fieldname': fieldname,
                    }).done(function(result) {

                        if(result.data) {
                            $('#publicDisplayUrl').html(result.data)
                        }

                });
            }

            $('#config').on('change', '.js-switchery', function () {

                let status, fieldname;
                let id_colla = $(this).data().id_colla;

                if($(this).hasClass('boards_enabled')){
                    fieldname = 'boards_enabled';
                } else if($(this).hasClass('public_display_enabled')) {
                    fieldname = 'public_display_enabled';
                } else if($(this).hasClass('member_session_expire')) {
                    fieldname = 'member_session_expire';
                } else if($(this).hasClass('member_edit_personal')) {
                    fieldname = 'member_edit_personal';
                }

                if($(this).hasClass('active')) {
                    $(this).removeClass("active");
                    $(this).addClass("inactive");
                    status = 0;
                } else if($(this).hasClass('inactive')) {
                    $(this).removeClass("inactive");
                    $(this).addClass("active");
                    status = 1;
                }
                setStatus(id_colla, status, fieldname);
            });

            let elems = Array.prototype.slice.call(document.querySelectorAll('.js-switchery'));
            elems.forEach(function (html) {
                new Switchery(html, {size: 'small'});
            });

            $("#periods-table").DataTable({
                "language": {!! trans('datatables.translation') !!},
                "stateSave": true,
                "stateDuration": -1
            });

            $('.btn-add-period').on('click', function (event)
            {
                $('#modalAddPeriod').modal('show');

                $('#modalAddPeriodContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                $.get( "{{ route('profile.colla.periods.add-period-modal') }}", function( data ) {
                    $('#modalAddPeriodContent').html( data );
                });
            });

            $('#periods-table').on('click','.btn-delete-period', function()
            {
                var id_period = $(this).data().id_period;

                var url = "{{ route('profile.colla.periods.destroy', ':id_period') }}";
                url = url.replace(':id_period', id_period)

                $('#formDelPeriod').attr('action', url);
                $('#modalDelPeriod').modal('show');
            });


            $("#periods-table").on('click', '.btn-edit-period', function (event)
            {
                var id_period = $(this).data().id_period;

                $('#modalAddPeriod').modal('show');

                $('#modalAddPeriodContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');


            var url = "{{ route('profile.colla.periods.edit-period-modal', ':id_period') }}";
                url = url.replace(':id_period',id_period);
                console.log(url);
                $.get( url, function( data ) {
                    $('#modalAddPeriodContent').html( data );
                });

            });


            $('body').on('focus',"input.datepicker", function(){
                $(this).datepicker({
                    @if (Auth()->user()->language=='ca')
                    language: 'ca',
                    @elseif(Auth()->user()->language=='es')
                    language: 'es',
                    @endif
                    format: "dd/mm/yyyy",
                    autoclose: true,
                });
            });

            $("#start_period").on('changeDate', function(event){
                $("input[name='start_period']").val($('#start_period').datepicker('getDates'));
            });

            $("#end_period").on('changeDate', function(event){
                $("input[name='end_period']").val($('#end_period').datepicker('getDates'));
            });
        });
    </script>
@endsection
