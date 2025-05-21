@extends('members.template.main')

@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
    <link href="{{ asset('css/modals/event-info.css') }}" rel="stylesheet">
@endsection

@section('content')

<div id="upcoming_events">
    <div class="block">
     
        <div class="block-header ">

            <div class="block-title">
                <h3 class="block-title">{!! $casteller->getDisplayName() !!} {!! \App\Helpers\Humans::readCastellerColumn($casteller, 'gender') !!}</h3>
            </div>

        @if($collaConfig->getMemberEditPersonalData() )  @php $permis = '' @endphp @else  @php $permis = 'disabled' @endphp @endif 

        </div>
        <div class="block-content">
            <a class="img-link img-link-zoom-in" href="{!! $casteller->getProfileImage('xl') !!}">
                <img class="img-avatar-rounded img-fluid" style="border-radius: 8px;" src="{!! $casteller->getProfileImage() !!}" alt="Avatar: {!! $casteller->getDisplayName() !!}">
            </a>            
            <hr> 
                <div class="col-sm-12">
                    <div class="row" style="line-height: 2.6">

                        <div class="block-content" id="profileContent">

                            @if(Auth::user()->getColla()->getConfig()->getBoardsEnabled())
                                <div class="row mb-15 ">
                                    @if ( $casteller->getPosition())
                                
                                        <div class="col-md-4">
                                            <label class="control-label">{!! trans('casteller.position') !!}</label>
                                            <p><span class="badge badge-info">{!! $casteller->getPosition()->getName() !!}</span></p>
                                        </div>
                                
                                    @endif
        
                                    <div class="col-md-4">
                                        <label class="control-label">{!! trans('casteller.relative_height') !!} (cm)</label>
                                        <input type="number" class="form-control" id="relative_height" name="relative_height" value="@if(isset($casteller)){!! $casteller->getRelativeHeight() !!}@endif" min="-250" max="250" disabled>
                                    </div>
        
                                    <div class="col-md-4">
                                        <label class="control-label">{!! trans('casteller.relative_shoulder_height') !!} (cm)</label>
                                        <input type="number" class="form-control" id="relative_shoulder_height" name="relative_shoulder_height" value="@if(isset($casteller)){!! $casteller->getRelativeShoulderHeight() !!}@endif" min="-200" max="200" disabled>
                                    </div>
                    
                                </div>
                                <hr>
                            @endif

                            {!! Form::open(array('id' => 'FormUpdateCasteller', 'url' => route('member.update', $casteller->id_casteller), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}

                                <div class="row mb-15">
                                    <div class="col-md-4">
                                        <label class="control-label">{!! trans('casteller.name') !!}</label>
                                        <input type="text" class="form-control" id="name" name="name" value="@if(isset($casteller)){!! old('name',$casteller->getName()) !!}@else {!! old('name') !!}@endif" {!! $permis !!}>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="control-label">{!! trans('casteller.last_name') !!}</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="@if(isset($casteller)){!! old('last_name',$casteller->getLastname()) !!}@else {!! old('last_name') !!}@endif" {!! $permis !!}>
                                    </div>
                                </div>
                
                                <div class="row mb-15">
                                    <div class="col-md-4">
                                        <label class="control-label">{!! trans('casteller.nationality') !!}</label>
                                        <input type="text" class="form-control" id="nationality" name="nationality" value="@if(isset($casteller)){!! old('nationality',$casteller->getNationality()) !!}@else {!! old('nationality') !!}@endif" {!! $permis !!}>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label">{!! trans('casteller.national_id_type') !!}</label>
                                        <select class="form-control" name="national_id_type" id="national_id_type"  {!! $permis !!}>
                                            <option value="dni"  @if(isset($casteller) && $casteller->getnationalidtype()=='dni'){{ old('dni') == "dni" ? 'selected' : "" }}@endif  {!! $permis !!}>{!! trans('casteller.dni') !!}</option>
                                            <option value="nie" @if(isset($casteller) && $casteller->getnationalidtype()=='nie'){{ old('nie') == "nie" ? 'selected' : "''" }}@endif  {!! $permis !!}>{!! trans('casteller.nie') !!}</option>
                                            <option value="passport" @if(isset($casteller) && $casteller->getnationalidtype()=='passport'){{ old('passport') == "passport" ? 'selected' : "''" }}@endif  {!! $permis !!}>{!! trans('casteller.passport') !!}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label">{!! trans('casteller.national_id_number') !!}</label>
                                        <input type="text" class="form-control" id="national_id_number" name="national_id_number" value="@if(isset($casteller)){!! old('national_id_number',$casteller->getNationalIdNumber()) !!}@else {!! old('national_id_number') !!}@endif" {!! $permis !!}>
                                    </div>
                                </div>

                                <div class="row mb-15">
                                    <div class="col-md-4">
                                        <label class="control-label">{!! trans('casteller.gender') !!}</label>
                                        <?php
                                        $selectedGender = isset($casteller) ? old('gender', $casteller->getGender()) : old('gender');
                                        ?>
                                        <select class="form-control" name="gender" id="gender" {!! $permis !!}>
                                        <option value="0" @if($selectedGender == 0) selected @endif>{!! trans('casteller.gender_female') !!}</option>
                                        <option value="1" @if($selectedGender == 1) selected @endif>{!! trans('casteller.gender_male') !!}</option>
                                        <option value="2" @if($selectedGender == 2) selected @endif>{!! trans('casteller.gender_nobinary') !!}</option>
                                        <option value="3" @if($selectedGender == 3) selected @endif>{!! trans('casteller.gender_nsnc') !!}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label">{!! trans('casteller.birthdate') !!}</label>
                                        <input type="text" class="form-control datepicker" id="birthdate" name="birthdate" value="@if(isset($casteller)){!! \App\Helpers\Humans::readCastellerColumn($casteller, 'birthdate') !!}@endif" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}"  {!! $permis !!}>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label">{!! trans('casteller.subscription_date') !!}</label>
                                        <input type="text" class="form-control" id="subscription_date" name="subscription_date" value="@if(isset($casteller)){!! \App\Helpers\Humans::readCastellerColumn($casteller, 'subscription_date') !!}@endif" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" disabled>

                                    </div>
                                </div>

                                <div class="row mb-15">

                                    <div class="col-md-5">
                                        <label class="control-label">{!! trans('casteller.num_soci') !!} </label>
                                        <input type="text" class="form-control" id="num_soci" name="num_soci" value="@if(isset($casteller)){!! old('num_soci',$casteller->getNumSoci()) !!}@else {!! old('num_soci') !!}@endif" min="-250" max="250" disabled>
                                    </div>
                                    <div class="col-md-7">
                                        <label class="control-label">{!! trans('casteller.photo') !!} <img class="img-avatar img-avatar32" style="border-radius: 8px;" src="{!! $casteller->getProfileImage() !!}" alt="Avatar: {!! $casteller->getDisplayName() !!}"></label>
                                        @if ( $permis == 'disabled' )
                                        @else
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input form-control" name="photo" id="photo" accept="image/png, image/jpeg">
                                                <label class="custom-file-label" for="photo">{!! trans('general.select_file') !!}</label>
                                            </div>
                                        @endif              
                                    </div>
                
                                </div>

                                <div class="row mb-15">
                                    <div class="col-md-4">
                                        <label class="control-label">{!! trans('general.phone') !!}</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="@if(isset($casteller)){!! old('phone',$casteller->getPhone()) !!}@else {!! old('phone') !!}@endif" {!! $permis !!}> 
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label">{!! trans('casteller.mobile_phone') !!}</label>
                                        <input type="text" class="form-control" id="mobile_phone" name="mobile_phone" value="@if(isset($casteller)){!! old('mobile_phone',$casteller->getPhoneMobile()) !!}@else {!! old('mobile_phone') !!}@endif" {!! $permis !!}>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label">{!! trans('casteller.emergency_phone') !!}</label>
                                        <input type="text" class="form-control" id="emergency_phone" name="emergency_phone" value="@if(isset($casteller)){!! old('emergency_phone',$casteller->getPhoneEmergency()) !!}@else {!! old('emergency_phone') !!}@endif" {!! $permis !!}>
                                    </div>
                                </div>
                
                                <div class="row mb-15">
                                    <div class="col-md-6">
                                        <label class="control-label">{!! trans('general.email') !!}</label>
                                        <input type="email" class="form-control" id="email" name="email" value="@if(isset($casteller)){!! old('email',$casteller->getEmail()) !!}@else {!! old('email') !!}@endif" {!! $permis !!}>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">{!! trans('general.email') !!} 2</label>
                                        <input type="email" class="form-control" id="email2" name="email2" value="@if(isset($casteller)){!! old('email2',$casteller->getEmail2()) !!}@else {!! old('email2') !!}@endif" {!! $permis !!}>
                                    </div>
                
                                </div>

                                <div class="row mb-15">
                                    <div class="col-md-9">
                                        <label class="control-label">{!! trans('casteller.address') !!}</label>
                                        <input type="text" class="form-control" id="address" name="address" value="@if(isset($casteller)){!! old('address',$casteller->getAddress()) !!}@else {!! old('address') !!}@endif" {!! $permis !!}>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">{!! trans('casteller.postal_code') !!}</label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code" value="@if(isset($casteller)){!! old('postal_code',$casteller->getZipCode()) !!}@else {!! old('postal_code') !!}@endif" {!! $permis !!}>
                                    </div>
                                </div>

                                <div class="row mb-15">
                                    <div class="col-md-3">
                                        <label class="control-label">{!! trans('casteller.city') !!}</label>
                                        <input type="text" class="form-control" id="city" name="city" value="@if(isset($casteller)){!! old('city',$casteller->getCity()) !!}@else {!! old('city') !!}@endif" {!! $permis !!}>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">{!! trans('casteller.comarca') !!}</label>
                                        <input type="text" class="form-control" id="comarca" name="comarca" value="@if(isset($casteller)){!! old('comarca',$casteller->getComarca()) !!}@else {!! old('comarca') !!}@endif" {!! $permis !!}>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">{!! trans('casteller.province') !!}</label>
                                        <input type="text" class="form-control" id="province" name="province" value="@if(isset($casteller)){!! old('province',$casteller->getProvince()) !!}@else {!! old('province') !!}@endif" {!! $permis !!}>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">{!! trans('casteller.country') !!}</label>
                                        <input type="text" class="form-control" id="country" name="country" value="@if(isset($casteller)){!! old('country',$casteller->getCountry()) !!}@else {!! old('country') !!}@endif" {!! $permis !!}>
                                    </div>
                                </div>


                                <div class="row mb-15 mt-30">
                                    <div class="col-md-12 text-left">
                                        @if ( $permis == 'disabled' )
                                            <label class="text-warning">{!! trans('config.members_no_edit_personal') !!}</label>
                                        @else
                                            <button form="FormUpdateCasteller" class="btn btn-primary"><i class="fa fa-save"></i> {!! trans('general.save') !!}</button> 
                                        @endif 
                                    </div>
                                    
                                </div>

                            {!! Form::close() !!}

                            <hr>
                        </div>
                    </div>
                </div>
        </div>
    </div>


    
</div>




@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <script src="{!! asset('js/plugins/magnific-popup/jquery.magnific-popup.js') !!}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    @if (Auth()->user()->getLanguage() === 'ca')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.ca.min.js') !!}"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/ca.min.js"></script>

    @elseif(Auth()->user()->getLanguage() === 'es')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js') !!}"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/es.min.js"></script>

    @endif
    <script type="text/javascript">
        $(function () {
            $.ajaxPrefilter(function(options, originalOptions, xhr) { // this will run before each request
                var token = $('meta[name="csrf-token"]').attr('content'); // or _token, whichever you are using

                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token); // adds directly to the XmlHttpRequest Object
                }
            });
        });
    </script>
     <script>
        $(function ()
        {

            $('#profileContent .tags').select2({language: "ca"});


            $('.img-link').magnificPopup({type:'image'});

            $("#profileContent .family").select2({
                tags: true,
                language: "ca"
            });

            $('#photo').on('change',function(){
                //get the file name
                let fieldVal = $(this).val();

                // Change the node's value by removing the fake path (Chrome)
                fieldVal = fieldVal.replace("C:\\fakepath\\", "");

                //replace the "Choose a file" label
                $(this).next('.custom-file-label').html(fieldVal);
            });

            $("#birthdate, #subscription_date").datepicker({
                @if (Auth()->user()->getLanguage() === 'ca')
                language: 'ca',
                @elseif(Auth()->user()->getLanguage() === 'es')
                language: 'es',
                @endif
                format: "dd/mm/yyyy",
                autoclose: true

            });
        });
    </script>

@endsection
