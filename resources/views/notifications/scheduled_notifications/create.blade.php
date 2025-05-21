@extends('template.main')
@section('title', trans('notifications.add_notifications'))

@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
    <link rel="stylesheet" href="{!! asset('js/plugins/cloudflare-switchery/css/switchery.min.css') !!}">
@endsection
@section('content')
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                @if(isset($notification))
                    <b>{!! trans('notifications.editar') !!}:</b> {!! $notification->title !!}
                @else
                    <b>{!! trans('notifications.add_notifications') !!}
                @endif
            </h3>
            <a href="{{  route('notifications.scheduled_notifications.list') }}" class="btn btn-primary btn-sm">{{ trans('notifications.tornar') }}</a>
        </div>
      <div class="block-content">
                  @if(isset($notification))
                      {!! Form::open(array('id' => 'FormUpdateNotification', 'url' => route('notifications.scheduled_notifications.update', $notification->id_scheduled_notification), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
                  @else
                      {!! Form::open(array('id' => 'FormAddNotification', 'url' => route('notifications.scheduled_notifications.add'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
                  @endif
                  <div class="row form-group" id="div_open_close_date">
                      <div class="col-md-6">
                          <div id="can_notification_date">
                            <label class="control-label">{!! trans('notifications.notification_date') !!}</label>
                            <div class="row">
                                <div class="col-md-4" id="div_notification_date">
                                    <input type="text" class="form-control" name="notification_date" id="notification_date" placeholder="{!! trans('general.select_date') !!}" value="@if(isset($notification)){!! old('notification_date', date('d/m/Y', strtotime($notification->getNotificationDate()))) !!}@else{!! old('notification_date') !!}@endif" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" required>
                                </div>
                                <div class="col-md-4" id="div_open_time">
                                        <div class="row">
                                        <div class="col-5" style="padding-left: 0; padding-right: 5px;">
                                            <select class="form-control" name="hour_notification_date" id="hour_notification_date" required>
                                                @php $oldHour = (isset($notification)) ? date('H', strtotime($notification->getNotificationDate())) : null @endphp
                                                @for ($i = 0; $i <= 23; $i++)
                                                    @php $hour = str_pad($i, 2, '0', STR_PAD_LEFT) @endphp
                                                    @if(isset($notification))
                                                        <option value="{!! $hour !!}" @if( old('hour_notification_date', $oldHour) == $hour) selected @endif>{!! $hour !!}</option>
                                                    @else
                                                        <option value="{!! $hour !!}" @if( old('hour_notification_date') == $hour) selected @endif>{!! $hour !!}</option>
                                                    @endif
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-sm-1 text-center" style="padding-left: 0; padding-right: 0; max-width: 1%;"><b>:</b></div>
                                        <div class="col-5" style="padding-left: 5px; padding-right: 0;">
                                            <select class="form-control" name="min_notification_date" id="min_notification_date" required>
                                                @php $oldMinute = (isset($notification)) ? date('i', strtotime($notification->getNotificationDate())) : null @endphp
                                                @for($i = 0; $i < 60; $i+=5)
                                                    @php $minute = str_pad($i, 2, '0', STR_PAD_LEFT) @endphp
                                                    @if(isset($notification))
                                                        <option value="{!! $minute !!}" @if( old('min_notification_date', $oldMinute) == $minute) selected @endif>{!! $minute!!}</option>
                                                    @else
                                                        <option value="{!! $minute !!}"  @if( old('min_notification_date') == $minute) selected @endif>{!! $minute!!}</option>
                                                    @endif
                                                @endfor
                                            </select>
                                        </div>
                                        </div>
                                </div>
                            </div>
                          </div>
                      </div>

                  </div>
                  <div class="row form-group" id="div_filters">
                      <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-2">
                            <label class="control-label">{!! trans('notifications.filter') !!}</label>
                            </div>
                            <div class="col-md-2">
                                <select name="filter_search_type" id="filter_search_type" class="selectize2 form-control">
                                    @php $oldFilterSearchType = isset($notification) ? $notification->getFilterSearchType() : '' ; @endphp
                                    @if (old('filter_search_type', $oldFilterSearchType) === \App\Enums\FilterSearchTypesEnum::AND)
                                        <option value="{!! \App\Enums\FilterSearchTypesEnum::OR !!}">{!! \App\Enums\FilterSearchTypesEnum::OR !!}</option>
                                        <option value="{!! \App\Enums\FilterSearchTypesEnum::AND !!}" selected>{!! \App\Enums\FilterSearchTypesEnum::AND !!}</option>
                                    @else
                                        <option value="{!! \App\Enums\FilterSearchTypesEnum::OR !!}" selected>{!! \App\Enums\FilterSearchTypesEnum::OR !!}</option>
                                        <option value="{!! \App\Enums\FilterSearchTypesEnum::AND !!}">{!! \App\Enums\FilterSearchTypesEnum::AND !!}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-5">
                                <select name="tags[]" id="tags" class="selectize2 form-control" multiple>
                                    @php $oldNotifications = isset($notification) ? $notification->tagsArray('id_tag') : [] ; @endphp
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->getId() }}" @if(in_array($tag->getId(), old('tags', $oldNotifications))) selected @endif>{{ $tag->getName() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                      </div>
                  </div>


                  <div class="row form-group" id="div_title">
                      <div class="col-md-12">
                          <label class="control-label">{!! trans('notifications.title') !!}</label>
                          <div class="row">
                            <div class="col-md-12" id="div_title">
                              <input type="text" name="title" id="title" class="form-control" value="@if(isset($notification)) {{ old('title', $notification->title) }} @else {{ old('title') }} @endif" placeholder="@if(isset($notification)) {{ $notification->title }} @else {!! trans('notifications.title') !!}@endif" mb-2 />
                            </div>
                          </div>
                      </div>
                    </div>

                    <div class="row form-group" id="div_body">
                      <div class="col-md-12">
                          <label class="control-label">{!! trans('notifications.body') !!}</label>
                          <div class="row">
                            <div class="col-md-12" id="div_body">
                                <textarea cols="10" rows="5" name="body" class="form-control" id="body" placeholder="{!! trans('notifications.body') !!}" required> @if(isset($notification)){{  old('body', $notification->body) }} @else {{ old('body') }} @endif</textarea>
                            </div>
                          </div>
                      </div>
                    </div>

                      @if(isset($notification))
                          <div class="row form-group" id="div_redactor">
                              <div class="col-md-12">
                                  <div class="row">
                                      <div class="col-md-12" id="div_body">
                                          <label class="control-label"><b>{!! trans('notifications.redactor') !!}:</b> {{  $redactor->name }} </label>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @endif

                  <div class="row form-group">
                      <div class="col-md-6 text-left">
                        @if(isset($notification))
                            <button class="btn btn-danger btn-delete-notification" data-id_notification="{!! $notification->getId() !!}"><i class="fa fa-trash-o"></i>{!! trans('general.delete') !!}</button>
                        @endif
                      </div>
                      <div class="col-md-6 text-right">
                            <button class="btn btn-primary"><i class="fa fa-save"></i> {!! trans('general.save') !!}</button>
                      </div>
                  </div>
                  {!! Form::close() !!}
                </div>
            </div>



@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <script src="{{ asset('js/plugins/cloudflare-switchery/js/switchery.js') }}"></script>
    <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    @if (Auth()->user()->language=='ca')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.ca.min.js') !!}"></script>
    @elseif(Auth()->user()->language=='es')
        <script type="text/javascript" src="{!! asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js') !!}"></script>
    @endif

    <script>
        $(function ()
        {
            $('.selectize2').select2({language: "ca"});

            let elems = Array.prototype.slice.call(document.querySelectorAll('.js-switchery'));
            elems.forEach(function (html) {
                new Switchery(html, {size: 'small'});
            });



            $('#notification_date').datepicker({
                @if (Auth()->user()->language=='ca')
                language: 'ca',
                @elseif(Auth()->user()->language=='es')
                language: 'es',
                @endif
                format: "dd/mm/yyyy",
                autoclose: true,
            });
        });
    </script>


@endsection
