@extends('template.main')

@php
    // Assuming $htmlContent contains your HTML content
    $dom = new DOMDocument();
    $dom->loadHTML($email_view);

    $bodyContent = '';

    // Extract only the body content
    $bodyTags = $dom->getElementsByTagName('body');
    foreach ($bodyTags as $bodyTag) {
        $bodyContent .= $dom->saveHTML($bodyTag);
    }

    $bodyContent = htmlspecialchars($bodyContent, ENT_QUOTES);
@endphp

@section('title', trans('notifications.notification'))
@section('css_before')

@section('content')
<div class="block">
    <div class="block-header block-header-default">
        <div class="block-title">
            <h3 class="block-title"><b>{{ $datatable->getTitle() }}</b></h3>
        </div>
    </div>
    <div class="block-content block-content-full">
        <div class="row">
            <div class="col-lg-12 font-weight-lighter text-right">
                <a href="{{ url()->previous() }}" class="btn btn-primary btn-sm">{{ trans('notifications.tornar') }}</a>
            </div>
            <br><br>
            <div class="col-lg-12" style="padding-right: 7px; border:dotted 1px grey;">
                <iframe id="dynamicIframe" srcdoc="{!! $bodyContent !!}" width=100% height="700" frameborder="0"></iframe>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-12">
                <div class="row" style="line-height: 2.6">
                    <div class="col-sm-4 font-w800 text-left  text-corporate-light">{!! trans('notifications.data') !!}</div>
                    <div class="col-sm-8 font-weight-lighter text-left">{{ date('d-m-Y H:m:s', strtotime($notification->created_at)) }}</div>
                </div>
                <div class="row" style="line-height: 2.6">
                    <div class="col-sm-4 font-w800 text-left text-corporate-light">{!! trans('notifications.type') !!}</div>
                    <div class="col-sm-8 font-weight-lighter text-left">{{ trans("notifications." . strtolower($notification->getTypeStr())) }}</div>
                </div>
                <div class="row" style="line-height: 2.6">
                    <div class="col-sm-4 font-w800 text-left text-corporate-light">{!! trans('notifications.author') !!}</div>
                    <div class="col-sm-8 font-weight-lighter text-left">{{ $notification->user != null ? $notification->user->getName() : "" }}</div>
                </div>
                <div class="row" style="line-height: 2.6">
                    <div class="col-sm-4 font-w800 text-left  text-corporate-light">{!! trans('notifications.title') !!}</div>
                    <div class="col-sm-8 font-weight-lighter text-left">{{ $notification->getTitle() }}</div>
                </div>
            </div>
        </div>
        <hr>
        <x-data-table :datatable="$datatable" />
    </div>
</div>

@endsection

@section('js')

@stack('datatables_js')
@endsection


