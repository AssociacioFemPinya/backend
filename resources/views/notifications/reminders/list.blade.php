@extends('template.main')

@section('title', trans('notifications.notifications'))
@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
@endsection
@section('content')

<x-data-table :datatable="$datatable"/>
@endsection

@section('js')
@stack('datatables_js')
@endsection
