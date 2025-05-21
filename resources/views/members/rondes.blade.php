@extends('members.template.main')

@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{!! asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css') !!}">
    <link href="{{ asset('css/modals/event-info.css') }}" rel="stylesheet">
@endsection

@section('content')

<div id="">
    <div class="block">
        @if ($event)
            <div class="block-header block-header-default">
                <div class="block-title">
                    <h3 class="block-title"><b>{!! ucfirst(trans('rondes.rondes')) !!}:</b> {!! $event->getName() !!} <span class="text-muted">({!! \App\Helpers\Humans::readEventColumn($event, 'start_date'); !!})</span></h3>
                </div>
            </div>
            @if($rondes->isEmpty())
                <div class="block-content">
                    <h3 class="block-title pb-20">{!! ucfirst(trans('rondes.no_rondes')) !!}</h3>
                </div>
            @else
                <div class="block-content">
                    <div class="row">
                        @foreach($rondes as $ronda)
                            @php $link = $ronda->getBoardEvent()->getPublicUrl($castellerId); @endphp
                            <div class="col col-12 pb-10"><a href="{!! $link !!}" target="_blank">{!! ucwords(trans('rondes.ronda')) . ' ' . $ronda->getRonda() !!}</a>:  {!! $ronda->getBoardEvent()->getDisplayName() !!}</div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="block-header block-header-default">
                <div class="block-title">
                    <h3 class="block-title">{!! ucfirst(trans('dashboard.first_event_no')) !!}</h3>
                </div>
            </div>
        @endif
    </div>
</div>




@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <script src="{!! asset('js/plugins/magnific-popup/jquery.magnific-popup.js') !!}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/buttons.html5.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.js') }}"></script>


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

@endsection
