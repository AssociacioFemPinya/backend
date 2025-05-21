@extends('template.main')

@section('title', trans('notifications.notification'))
@section('content')
<div class="row">
    <div class="col-lg-12" style="padding-right: 7px;">
        <div class="block">
            <div class="block-content">
                <div class="row">
                        <div class="col-sm-2 font-w800 text-left  text-corporate-light">
                            {!! trans('notifications.notification') !!}
                        </div>
                        <div class="col-sm-8 font-weight-lighter text-left">
                            {{ $scheduledNotification->id }}
                        </div>
                        <div class="col-sm-2 font-weight-lighter text-left">
                            <a href="{{ url()->previous() }}" class="btn btn-primary btn-sm">{{ trans('notifications.tornar') }}</a>
                        </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" style="line-height: 2.6">
                            <div class="col-sm-4 font-w800 text-left  text-corporate-light">{!! trans('notifications.data') !!}</div>
                            <div class="col-sm-8 font-weight-lighter text-left">{{ $scheduledNotification->getCreatedAt() }}</div>
                        </div>
                        <div class="row" style="line-height: 2.6">
                            <div class="col-sm-4 font-w800 text-left  text-corporate-light">{!! trans('notifications.open_date') !!}</div>
                            <div class="col-sm-8 font-weight-lighter text-left">{{ $scheduledNotification->getNotificationDate() }}</div>
                        </div>
                        <div class="row" style="line-height: 2.6">
                            <div class="col-sm-4 font-w800 text-left  text-corporate-light">{!! trans('notifications.tags') !!}</div>
                            <div class="col-sm-8 font-weight-lighter text-left">{{ join(", ", $scheduledNotification->tagsArray("name")) }}</div>
                        </div>
                        <div class="row" style="line-height: 2.6">
                            <div class="col-sm-4 font-w800 text-left text-corporate-light">{!! trans('notifications.author') !!}</div>
                            <div class="col-sm-8 font-weight-lighter text-left">{{ $scheduledNotification->autoria }}</div>
                        </div>
                        <div class="row" style="line-height: 2.6">
                            <div class="col-sm-4 font-w800 text-left  text-corporate-light">{!! trans('notifications.title') !!}</div>
                            <div class="col-sm-8 font-weight-lighter text-left">{{ $scheduledNotification->title }}</div>
                        </div>
                        <div class="row" style="line-height: 2.6">
                            <div class="col-sm-4 font-w800 text-left  text-corporate-light">{!! trans('notifications.body') !!}</div>
                            <div class="col-sm-8 font-weight-lighter text-left">
                                {{ $scheduledNotification->body }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
@stack('datatables_js')
@endsection
