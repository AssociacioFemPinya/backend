{!! Form::open(array('id' => 'FormUpdateCasteller', 'url' => route('castellers.update', $casteller->id_casteller), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
<ul id="tabs" class="nav nav-tabs nav-tabs-alt" data-toggle="tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link {!! $active_tab !!} @if ($active_tab == 'basic'){!! ' active ' !!} @endif" href="#btabs-static-home" data-tab="basic">
            <i class="fa fa-address-book-o"></i> {!! trans('casteller.card_basic') !!}
        </a>
    </li>

    <li class="nav-item">
        @can('view casteller personals')
        <a class="nav-link @if ($active_tab == 'advanced'){!! ' active ' !!} @endif" href="#btabs-static-profile" data-tab="advanced">
            <i class="fa fa-address-card-o"></i> {!! trans('casteller.card_avanced') !!}
        </a>
        @endcan
    </li>
</ul>

<div class="block-content tab-content">
    <!-- #1 -->
    <div class="tab-pane {!! $active_tab !!} @if ($active_tab == 'basic'){!! ' active ' !!} @endif" id="btabs-static-home" role="tabpanel">

        <div class="row mb-15">
            <div class="col-md-4">
                <label class="control-label">{!! trans('casteller.alias') !!} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="alias" name="alias" value="@if(isset($casteller)){!! $casteller->alias !!}@endif">
            </div>
            <div class="col-md-3">
                <label class="control-label">{!! trans('casteller.status') !!} <span class="text-danger">*</span></label>
                <select class="form-control" name="status" id="status" required>
                    @foreach ($statuses as $num => $status)
                        @if (isset($casteller) && $casteller->getStatus() === $num)
                            <option value="{{ $num }}" selected>{{ $status }}</option>
                        @else
                            <option value="{{ $num }}">{{ $status }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <hr>
        @if(Auth::user()->getColla()->getConfig()->getBoardsEnabled())
            <div class="row mb-15 ">
                <div class="col-md-3">
                    <label class="control-label">{!! trans('casteller.position') !!}</label>
                    <select class="form-control" name="position" id="position">
                        <option value="">{!! trans('casteller.select_position') !!}</option>
                        @foreach($positions as $position)
                            @if(!is_null($casteller->getPosition()) && $casteller->getPosition()->getValue() === $position->getValue())
                                <option value="{!! $position->getId() !!}" selected>{!! $position->getName() !!}</option>
                            @else
                                <option value="{!! $position->getId() !!}">{!! $position->getName() !!}</option>
                            @endif

                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="control-label">{!! trans('casteller.height') !!} (cm)</label>
                    <input type="number" class="form-control" id="height" name="height" value="@if(isset($casteller)){!! $casteller->getHeight() !!}@endif" min="-400" max="400">
                </div>
                <div class="col-md-3">
                    <label class="control-label">{!! trans('casteller.weight') !!} (Kg)</label>
                    <input type="number" class="form-control" id="weight" name="weight" value="@if(isset($casteller)){!! $casteller->getWeight() !!}@endif" min="-200" max="200" step=".01">
                </div>
                <div class="col-md-3">
                    <label class="control-label">{!! trans('casteller.shoulder_height') !!} (cm)</label>
                    <input type="number" class="form-control" id="shoulder_height" name="shoulder_height" value="@if(isset($casteller)){!! $casteller->getShoulderHeight() !!}@endif" min="-400" max="400">
                </div>



            </div>

            <div class="row mb-15 ">
                <div class="col-md-3">
                </div>
                <div class="col-md-3">
                    <label class="control-label">{!! trans('casteller.relative_height') !!} (cm)</label>
                    <input type="number" class="form-control" id="relative_height" name="relative_height" value="@if(isset($casteller)){!! $casteller->getRelativeHeight() !!}@endif" readonly>
                </div>
                <div class="col-md-3">
                </div>
                <div class="col-md-3">
                    <label class="control-label">{!! trans('casteller.relative_shoulder_height') !!} (cm)</label>
                    <input type="number" class="form-control" id="relative_shoulder_height" name="relative_shoulder_height" value="@if(isset($casteller)){!! $casteller->getRelativeShoulderHeight() !!}@endif" readonly>
                </div>

            </div>
            <hr>
        @endif
        <h3 class="block-title"> {!! trans('general.tags') !!} </h3>
        <div class="row form-group">
                @if (count($tags_groups) == 1)
                    {{-- START - ONLY ONE TAG GROUP --}}
                    <div class="col-md-12">
                        <select class="tags form-control" placeholder="{!! trans('castellers.without_tags') !!}" name="tags[]" multiple>
                            @foreach ($tags as $tag)
                                @if (isset($casteller) && in_array($tag->value, $casteller->tagsArray('value')))
                                    <option value="{{ $tag->getId() }}" selected>{{ $tag->getName() }}</option>
                                @else
                                    <option value="{{ $tag->getId() }}">{{ $tag->getName() }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    {{-- END - ONLY ONE TAG GROUP --}}
                @else
                    {{-- START - MULTIPLE TAG GROUPS --}}
                    @foreach ($tags_groups as $tag_group)
                        <div class="col-md-6">
                            <label class="control-label">
                                @if (!$tag_group->getGroup())
                                    {!! trans('casteller.no_group') !!}
                                @else
                                    @if (in_array($tag_group->group, [1,2,3,4,5,6] ))
                                        {!! trans('casteller.group').' '.$tag_group->getGroup() !!}
                                    @else
                                        {!! $tag_group->getGroup() !!}
                                    @endif

                                @endif
                            </label>

                            <select class="tags form-control" style="width: 100%;" placeholder="{!! trans('casteller.without_tags') !!}" name="tags[]" multiple>
                                @foreach ($tags as $tag)
                                    @if ($tag->getGroup() === $tag_group->getGroup())
                                        @if (in_array($tag->getValue(), $casteller->TagsArray('value')))
                                            <option value="{{ $tag->getId() }}" selected>{{ $tag->getName() }}</option>
                                        @else
                                            <option value="{{ $tag->getId() }}">{{ $tag->getName() }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>

                        </div>
                    @endforeach
                    {{-- END - MULTIPLE TAG GROUPS --}}
                @endif
            </div>
    </div>
    <!-- END #1 -->


    <!-- #2 -->
    @can('view casteller personals')
        <div class="tab-pane {!! $active_tab !!} @if ($active_tab == 'advanced'){!! ' active ' !!} @endif" id="btabs-static-profile" role="tabpanel">
            <div>
                {{-- ESCRITURA   --}}
                    <p class="alert alert-info">{!!  trans('casteller.card_info_avanced') !!}</p>
                    <div class="row mb-15">
                        <div class="col-md-4">
                            <label class="control-label">{!! trans('casteller.name') !!}</label>
                            <input type="text" class="form-control" id="name" name="name" value="@if(isset($casteller)){!! old('name',$casteller->getName()) !!}@else {!! old('name') !!}@endif">
                        </div>
                    </div>


                    <div class="row mb-15">
                        <div class="col-md-8">
                            <label class="control-label">{!! trans('casteller.last_name') !!}</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="@if(isset($casteller)){!! old('last_name',$casteller->getLastname()) !!}@else {!! old('last_name') !!}@endif">
                        </div>
                    </div>

                    <div class="row mb-15">
                        <div class="col-md-4">
                            <label class="control-label">{!! trans('casteller.nationality') !!}</label>
                            <input type="text" class="form-control" id="nationality" name="nationality" value="@if(isset($casteller)){!! old('nationality',$casteller->getNationality()) !!}@else {!! old('nationality') !!}@endif">
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">{!! trans('casteller.national_id_type') !!}</label>
                            <select class="form-control" name="national_id_type" id="national_id_type">
                                <option value="dni" @if(isset($casteller) && $casteller->getnationalidtype()=='dni'){{ old('dni') == "dni" ? 'selected' : "" }}@endif>{!! trans('casteller.dni') !!}</option>
                                <option value="nie" @if(isset($casteller) && $casteller->getnationalidtype()=='nie'){{ old('nie') == "nie" ? 'selected' : "''" }}@endif>{!! trans('casteller.nie') !!}</option>
                                <option value="passport" @if(isset($casteller) && $casteller->getnationalidtype()=='passport'){{ old('passport') == "passport" ? 'selected' : "''" }}@endif>{!! trans('casteller.passport') !!}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">{!! trans('casteller.national_id_number') !!}</label>
                            <input type="text" class="form-control" id="national_id_number" name="national_id_number" value="@if(isset($casteller)){!! old('national_id_number',$casteller->getNationalIdNumber()) !!}@else {!! old('national_id_number') !!}@endif">
                        </div>
                    </div>
                    <div class="row mb-15">
                        <div class="col-md-4">
                            <label class="control-label">{!! trans('casteller.gender') !!}</label>
                            <?php
                            $selectedGender = isset($casteller) ? old('gender', $casteller->getGender()) : old('gender');
                            ?>
                            <select class="form-control" name="gender" id="gender">
                            <option value="0" @if($selectedGender == 0) selected @endif>{!! trans('casteller.gender_female') !!}</option>
                            <option value="1" @if($selectedGender == 1) selected @endif>{!! trans('casteller.gender_male') !!}</option>
                            <option value="2" @if($selectedGender == 2) selected @endif>{!! trans('casteller.gender_nobinary') !!}</option>
                            <option value="3" @if($selectedGender == 3) selected @endif>{!! trans('casteller.gender_nsnc') !!}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">{!! trans('casteller.birthdate') !!}</label>
                            <input type="text" class="form-control datepicker" id="birthdate" name="birthdate" value="@if(isset($casteller)){!! \App\Helpers\Humans::readCastellerColumn($casteller, 'birthdate') !!}@endif" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}">
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">{!! trans('casteller.subscription_date') !!}</label>
                            <input type="text" class="form-control datepicker"  id="subscription_date" name="subscription_date" value="@if(isset($casteller)){!! \App\Helpers\Humans::readCastellerColumn($casteller, 'subscription_date') !!}@endif" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}">
                        </div>
                    </div>
                    <div class="row mb-15">
                         {{--  TODO family no esta programat. Es desactivat fins dessenvolupar correctament   --}}
                        {{--  <div class="col-md-4">
                            <label class="control-label">{!! trans('casteller.family') !!}</label>
                            <select class="form-control family" name="family" id="family" style="width: 100%;">
                                @if(!empty($casteller->family) || !is_null($casteller->family))
                                    <option value="{!! $casteller->family !!}">{!! $casteller->family !!}</option>
                                @else
                                    <option value="" selected>&nbsp;&nbsp;&nbsp;</option>
                                @endif
                                <option value="">&nbsp;&nbsp;&nbsp;</option>
                                @foreach($families as $family)
                                    <option value="{!! $family !!}">{!! $family !!}</option>
                                @endforeach
                            </select>
                        </div>  --}}
                        <div class="col-md-5">
                            <label class="control-label">{!! trans('casteller.num_soci') !!} </label>
                            <input type="text" class="form-control" id="num_soci" name="num_soci" value="@if(isset($casteller)){!! old('num_soci',$casteller->getNumSoci()) !!}@else {!! old('num_soci') !!}@endif">
                        </div>
                        <div class="col-md-7">
                            <label class="control-label">{!! trans('casteller.photo') !!} <img class="img-avatar img-avatar32" style="border-radius: 8px;" src="{!! $casteller->getProfileImage() !!}" alt="Avatar: {!! $casteller->getDisplayName() !!}"></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input form-control" name="photo" id="photo" accept="image/png, image/jpeg">
                                <label class="custom-file-label" for="photo">{!! trans('general.select_file') !!}</label>
                            </div>
                        </div>

                    </div>
                    <div class="row mb-15">
                        <div class="col-md-4">
                            <label class="control-label">{!! trans('general.phone') !!}</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="@if(isset($casteller)){!! old('phone',$casteller->getPhone()) !!}@else {!! old('phone') !!}@endif">
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">{!! trans('casteller.mobile_phone') !!}</label>
                            <input type="text" class="form-control" id="mobile_phone" name="mobile_phone" value="@if(isset($casteller)){!! old('mobile_phone',$casteller->getPhoneMobile()) !!}@else {!! old('mobile_phone') !!}@endif">
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">{!! trans('casteller.emergency_phone') !!}</label>
                            <input type="text" class="form-control" id="emergency_phone" name="emergency_phone" value="@if(isset($casteller)){!! old('emergency_phone',$casteller->getPhoneEmergency()) !!}@else {!! old('emergency_phone') !!}@endif">
                        </div>
                    </div>


                    <div class="row mb-15">
                        <div class="col-md-6">
                            <label class="control-label">{!! trans('general.email') !!}</label>
                            <input type="email" class="form-control" id="email" name="email" value="@if(isset($casteller)){!! old('email',$casteller->getEmail()) !!}@else {!! old('email') !!}@endif">
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">{!! trans('general.email') !!} 2</label>
                            <input type="email" class="form-control" id="email2" name="email2" value="@if(isset($casteller)){!! old('email2',$casteller->getEmail2()) !!}@else {!! old('email2') !!}@endif">
                        </div>

                    </div>
                    <div class="row mb-15">
                        <div class="col-md-9">
                            <label class="control-label">{!! trans('casteller.address') !!}</label>
                            <input type="text" class="form-control" id="address" name="address" value="@if(isset($casteller)){!! old('address',$casteller->getAddress()) !!}@else {!! old('address') !!}@endif">
                        </div>
                        <div class="col-md-3">
                            <label class="control-label">{!! trans('casteller.postal_code') !!}</label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" value="@if(isset($casteller)){!! old('postal_code',$casteller->getZipCode()) !!}@else {!! old('postal_code') !!}@endif">
                        </div>
                    </div>
                    <div class="row mb-15">
                        <div class="col-md-3">
                            <label class="control-label">{!! trans('casteller.city') !!}</label>
                            <input type="text" class="form-control" id="city" name="city" value="@if(isset($casteller)){!! old('city',$casteller->getCity()) !!}@else {!! old('city') !!}@endif">
                        </div>
                        <div class="col-md-3">
                            <label class="control-label">{!! trans('casteller.comarca') !!}</label>
                            <input type="text" class="form-control" id="comarca" name="comarca" value="@if(isset($casteller)){!! old('comarca',$casteller->getComarca()) !!}@else {!! old('comarca') !!}@endif">
                        </div>
                        <div class="col-md-3">
                            <label class="control-label">{!! trans('casteller.province') !!}</label>
                            <input type="text" class="form-control" id="province" name="province" value="@if(isset($casteller)){!! old('province',$casteller->getProvince()) !!}@else {!! old('province') !!}@endif">
                        </div>
                        <div class="col-md-3">
                            <label class="control-label">{!! trans('casteller.country') !!}</label>
                            <input type="text" class="form-control" id="country" name="country" value="@if(isset($casteller)){!! old('country',$casteller->getCountry()) !!}@else {!! old('country') !!}@endif">
                        </div>
                    </div>
                    <div class="row mb-15">
                        <div class="col-md-12">
                            <label class="control-label">{!! trans('casteller.comments') !!}</label>
                            <textarea class="form-control" name="comments" id="comments" cols="30" rows="5">@if(isset($casteller)){!! old('comments',$casteller->getComments()) !!}@else{!! old('comments') !!}@endif</textarea>
                        </div>
                    </div>
            </div>
        </div>
    @endcan

    <!-- END #2 -->

</div>



<div class="row mb-15 mt-30">
    @if( Auth::user()->can('edit BBDD') || Auth::user()->can('edit casteller personals') )
        {{-- AMB PERMISOS --}}
        <div class="col-md-6 text-left">
            <button form="FormUpdateCasteller" class="btn btn-primary"><i class="fa fa-save"></i> {!! trans('general.save') !!}</button>
            <a href="{{ route('castellers.list') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> {!! trans('general.back') !!} </a>
        </div>
        <div class="col-md-6 text-right">
            <button class="btn btn-danger btn-delete-casteller"><i class="fa fa-trash-o"></i> {!! trans('general.delete') !!}</button>
        </div>
    @endif
</div>

<input type="hidden" id="active_tab" name="active_tab" value="basic">
{!! Form::close() !!}
