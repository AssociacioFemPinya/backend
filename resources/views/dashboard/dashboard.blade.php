@extends('template.main')

@section('title', 'Dashboard')
@section('css_after')
<style>
    .btn.disabled{
        cursor: auto;
    }
    #pinyes-icon{
        width: 29px;
        height: 29px;
        background-image: url({!! asset('media/img/ico_pinya_gray.svg') !!});
        background-size: cover;
        background-repeat: no-repeat;
        display: inline-block;
    }
    #pinyes-icon:hover {
        background-image: url({!! asset('media/img/ico_pinya_gray_focus.svg') !!});
    }
    .block-title{
        font-weight: 600 !important;
    }
    .block-header{
        min-height:70px;
    }
    .main-title{
        font-size:1.5rem;
    }
    #attendance-list div{
        margin-bottom:0px;
    }

    #event-attendances .row{
        align-items: center;
    }
    .attendance-list-alias{
        flex-wrap:wrap;
        align-self: flex-start;
    }
    .attendance-list-alias span{
        width:100%;
        padding-bottom:5px;
    }

    @media (min-width: 992px) {
        .attendance-list-alias span{
            width:50%;
        }
    }

    #event-attendances .event-buttons .btn{
        width:34px;
        max-width:34px;
        font-size:85%;
        padding-right:0.5rem;
        padding-left:0.5rem;
    }
    #event-attendances .event div{
        padding:0;
        margin:0;
    }

    #event-attendances .event-reminder{
        cursor: pointer;
    }

    @media (max-width: 576px) {
        #event-attendances .event-name{
            padding-bottom: 1rem !important;
        }
    }

    .row.notification-title {
        font-size: 1.1rem;
    }

    .row.notification-body .title{
        font-size: 1.2rem;
    }

    @media (min-width: 576px) {
        .row.notification-body div, .row.notification-detail div{
            padding-left:0;
        }
    }

    </style>

@endsection

@section('content')


@auth()
    <!-- Main Container -->
    <main id="main-container">
            <div class="row invisible" data-toggle="appear">
                <!-- COLUMN #1 -->
                    <div id="first-col" class="col-xl-8 d-flex align-items-stretch pr-1">
                        <div class="w-100">

                            <!-- title -->
                                @can('view events')
                                    @if($displayEvent)
                                        <div class="block block-rounded">
                                            <div class="block-header bg-gray">
                                                <h2 class="block-title">
                                                    <span class="main-title">{!! $displayEvent->getName() !!} </span>
                                                    <span class="text-muted"> ({!! \App\Helpers\Humans::readEventColumn($displayEvent, 'start_date'); !!})</span>
                                                </h2>
                                                <div class="block-options">
                                                    @can('edit events')
                                                        <a href="{{ route('events.edit',  $displayEvent->getId()) }}">
                                                            <i class="fa-solid fa-pencil fa-2x btn-block-option"></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>

                                    @else
                                        <div class="block block-rounded bg-gray">
                                            <div class="block-content block-content-full">
                                                <div class="row">
                                                    <div class="col">
                                                        {!! trans('dashboard.first_event_no') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="block block-rounded bg-gray">
                                        <div class="block-content block-content-full">
                                            <div class="row">
                                                <div class="col">
                                                    {!! trans('dashboard.nothing_to_show') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            <!-- end title -->

                            @if(Auth::user()->getColla()->getConfig()->getBoardsEnabled())

                            <!-- widget EventBoards -->
                                @can('view boards')
                                    <div id="event_boards" class="block block-rounded">
                                        <div class="block-header bg-gray">
                                            <h3 class="block-title">
                                                {!! trans('dashboard.widgets_boards') !!}
                                            </h3>
                                            <div class="block-options align-items-center" style="display:flex;">
                                                @if($displayEvent)
                                                    @if($eventboards && count($eventboards)>0)
                                                        <a id="pinyes-icon" href="{{ route('event.board',  $displayEvent->getId()) }}"> </a>
                                                    @else
                                                        <a id="pinyes-icon" class="empty-boards" href="#"> </a>
                                                    @endif
                                                    <a href="{{ route('event.rondes',  $displayEvent->getId()) }}">
                                                        <i class="fa-solid fa-list-ol fa-2x btn-block-option"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        @if($eventboards && count($eventboards)>0)
                                            <div class="block-content block-content-full pb-0" >
                                                <div class="row text-center">
                                                @foreach($eventboards as $eventboard)
                                                    <div class="col-4 col-sm-3 pb-4">
                                                        @can('edit boards')
                                                            <a href="{{ route('event.board', $displayEvent->getId().'/'.$eventboard->getId()) }}">
                                                                <span class="btn btn-alt-primary">
                                                                    {{ $eventboard->getDisplayName() }}
                                                                </span>
                                                            </a>
                                                        @else
                                                            <span class="btn btn-alt-secondary disabled">
                                                                {{ $eventboard->getDisplayName() }}
                                                            </span>
                                                        @endcan
                                                    </div>
                                                @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="block-content block-content-full">
                                                <div>
                                                    {!! trans('dashboard.nothing_to_show') !!}
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                @endcan
                            <!-- end widget EventBoards -->

                            @endif
                            <!-- widget attendance -->
                                @can('view events')
                                    <div id="attendance" class="block block-rounded">
                                        <div class="block-header bg-gray">
                                            <h3 class="block-title">
                                                {!! trans('dashboard.widgets_attendance') !!}
                                            </h3>
                                            @if( $displayEvent )
                                                <div class="block-options">
                                                    <a class="" href="{{ route('event.attendance', $displayEvent->getId() ) }}">
                                                        <i class="fa-solid fa-users fa-2x btn btn-rounded btn-block-option"></i>
                                                    </a>
                                                    <a class="" href="{{ route('event.attendance.verify', $displayEvent->getId() ) }}" target="_blank">
                                                        <i class="fa-solid fa-clipboard-user fa-2x btn btn-rounded btn-block-option"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="block-content block-content-full">
                                            @if( $displayEvent )
                                                <div class="row text-center">
                                                    <div class="col-4 block font-size-h1">
                                                        <i class="fa-solid fa-check text-success"></i> {!! $displayEvent->countAttenders()['ok'] !!}
                                                    </div>
                                                    <div class="col-4 block font-size-h1">
                                                        <i class="fa-solid fa-close text-danger"></i> {!! $displayEvent->countAttenders()['nok'] !!}
                                                    </div>
                                                    <div class="col-4 block font-size-h1">
                                                        <i class="fa-solid fa-question text-warning"></i> {!! $displayEvent->countAttenders()['unknown'] !!}
                                                    </div>
                                                </div>
                                                @if($attendances && !empty($attendances))
                                                    <div id="attendance-list" class="row block text-left">
                                                        <div class="col-4 d-flex attendance-list-alias">
                                                            @foreach($attendances['ok'] as $alias)
                                                                <span>{{ $alias }}</span>
                                                            @endforeach
                                                        </div>
                                                        <div class="col-4 d-flex border-left attendance-list-alias">
                                                            @foreach($attendances['nok'] as $alias)
                                                                <span>{{ $alias }}</span>
                                                            @endforeach
                                                        </div>
                                                        <div class="col-4 d-flex border-left attendance-list-alias">
                                                            @foreach($attendances['unknown'] as $alias)
                                                                <span>{{ $alias }}</span>
                                                            @endforeach
                                                        </div>

                                                    </div>
                                                @endif
                                            @else
                                                <div>
                                                    {!! trans('dashboard.nothing_to_show') !!}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endcan
                            <!-- end widget attendance -->

                        </div>
                    </div>
                <!-- end COLUM #1 -->

                <!-- COLUM #2 -->
                    <div id="second-col" class="col-xl-4 d-flex align-items-stretch pl-1">
                        <div class="w-100">
                            <!-- widget notifications -->
                            @can('view notifications')
                                <div id="notifications" class="block block-rounded">
                                    <div class="block-header bg-corporate">
                                        <h3 class="block-title">
                                            {!! trans('dashboard.widgets_notifications') !!}
                                        </h3>
                                        <div class="block-options">
                                            <a href="{{ route('notifications.register.list') }}">
                                                <i class="fa-regular fa-envelope fa-2x btn btn-rounded btn-block-option"></i>
                                            </a>
                                        </div>
                                    </div>
                                        <div class="block-content block-content-full">
                                            @if($notifications && count($notifications)>0)
                                            @php $lastElement = array_key_last($notifications) @endphp
                                                @foreach($notifications as $index => $notification)
                                                    <div class="row notification-title pb-10 align-items-center">
                                                        <div class="col-2 text-center">
                                                            <img src="{!! $notification['authorPhoto']!!}" class="img-avatar img-avatar32">
                                                        </div>
                                                        <div class="col-10 pl-0">
                                                                <span class="font-w600 text-primary">{{ $notification['authorName'] }}</span>
                                                                <em class="text-muted">&bull; {{ $notification['date'] }}</em>
                                                        </div>
                                                    </div>
                                                    <div class="row notification-body">
                                                        <div class="col-12 col-sm-10 offset-sm-2 font-w600 pb-10 title">
                                                            {{ $notification['title'] }}
                                                        </div>
                                                        <div class="col-12 col-sm-10 offset-sm-2 pb-10">
                                                            {{ $notification['body'] }}
                                                        </div>
                                                    </div>
                                                    <div class="row notification-detail">
                                                        <div class="col-12 col-sm-10 offset-sm-2 ">
                                                            <a href="{{ route('notifications.details', $notification['id'] ) }}">{{ trans('notifications.view_details') }}</a>
                                                        </div>
                                                    </div>
                                                    @if ($index !== $lastElement)
                                                        <hr>
                                                    @endif
                                                @endforeach
                                            @else
                                                <div>
                                                    {!! trans('dashboard.nothing_to_show') !!}
                                                </div>
                                            @endif
                                        </div>
                                </div>
                            @endcan
                            <!-- end widget notifications  -->

                            <!-- widges counters -->
                                <div id="counters" class="block  block-rounded">
                                    <div class="block-header bg-corporate">
                                        <h3 class="block-title">{!! trans('dashboard.widgets_stats_counters') !!}</h3>
                                        <div class="block-options">
                                            <i class="fa-solid fa-gauge-high fa-2x"></i>
                                        </div>
                                    </div>
                                    <div class="block-content block-content-full">
                                        <div class="row text-center">
                                                    <!-- Row #counter -->
                                                            <div class="col-4 col-sm-2">
                                                                <span class="badge badge-primary badge-pill"> {!! $compta['users'] !!} </span>
                                                                <div class="pt-2">
                                                                    @can('view colla')
                                                                        <a  href="{{ route('profile.colla') }}">
                                                                            <i class="fa-solid fa-user fa-2x btn btn-rounded btn-alt-primary"></i>
                                                                        </a>
                                                                    @else
                                                                        <i class="fa-solid fa-user fa-2x btn btn-rounded btn-alt-secondary disabled"></i>
                                                                    @endcan
                                                                </div>

                                                            </div>

                                                            <div class="col-4 col-sm-2">
                                                                @if($compta['castellers'] < $compta['maxCastellers'])
                                                                    <span class="badge badge-primary badge-pill"> {!! $compta['castellers'] !!} / {!! $compta['maxCastellers']!!}</span>
                                                                @else
                                                                    <span class="badge badge-danger badge-pill"> {!! $compta['castellers'] !!} / {!! $compta['maxCastellers']!!} </span>
                                                                @endif
                                                                <div class="pt-2">
                                                                    @can('view BBDD')
                                                                        <a href="{{ route('castellers.list') }}">
                                                                            <i class="fa-regular fa-address-card fa-2x btn btn-rounded btn-alt-primary"></i>
                                                                        </a>
                                                                    @else
                                                                        <i class="fa-regular fa-address-card fa-2x btn btn-rounded btn-alt-secondary disabled"></i>
                                                                @endcan
                                                                </div>
                                                            </div>

                                                            <div class="col-4 col-sm-2">
                                                                <span class="badge badge-primary badge-pill"> {!! $compta['membersTelegram'] !!} </span>
                                                                <div class="pt-2">
                                                                    @can('view casteller config')
                                                                        <a href="{{ route('castellers.config.list') }}">
                                                                            <i class="fa-brands fa-telegram fa-2x btn btn-rounded btn-alt-primary"></i>
                                                                        </a>
                                                                    @else
                                                                        <i class="fa-brands fa-telegram fa-2x  btn btn-rounded btn-alt-secondary disabled"></i>
                                                                    @endcan

                                                                </div>
                                                            </div>

                                                            <div class="col-4 col-sm-2">
                                                                <span class="badge badge-primary badge-pill"> {!! $compta['membersWeb'] !!} </span>
                                                                <div class="pt-2">
                                                                    @can('view casteller config')
                                                                        <a href="{{ route('castellers.config.list') }}">
                                                                            <i class="fa-solid fa-mobile-screen-button fa-2x btn btn-rounded btn-alt-primary"></i>
                                                                        </a>
                                                                    @else
                                                                        <i class="fa-solid fa-mobile-screen-button fa-2x btn btn-rounded btn-alt-secondary disabled"></i>
                                                                @endcan
                                                                </div>
                                                            </div>

                                                            <div class="col-4 col-sm-2">
                                                                <span class="badge badge-primary badge-pill"> {!! $compta['events'] !!} </span>
                                                                <div class="pt-2">
                                                                    @can('view events')
                                                                        <a href="{{ route('events.list') }}">
                                                                            <i class="fa-regular fa-calendar-days fa-2x btn btn-rounded btn-alt-primary"></i>
                                                                        </a>
                                                                    @else
                                                                        <i class="fa-regular fa-calendar-days fa-2x btn btn-rounded btn-alt-secondary disabled"></i>
                                                                    @endcan
                                                                </div>
                                                            </div>

                                                            <div class="col-4 col-sm-2">
                                                                <span class="badge badge-primary badge-pill"> {!! $compta['notifications'] !!} </span>
                                                                <div class="pt-2">
                                                                    @can('view notifications')
                                                                        <a class="" href="{{ route('notifications.scheduled_notifications.list') }}">
                                                                            <i class="fa-regular fa-envelope fa-2x btn btn-rounded btn-alt-primary"></i>
                                                                        </a>
                                                                    @else
                                                                        <i class="fa-regular fa-envelope fa-2x btn btn-rounded btn-alt-secondary disabled"></i>
                                                                    @endcan
                                                                </div>
                                                            </div>

                                                    <!-- END Row #counter -->
                                        </div>
                                    </div>
                                </div>
                            <!-- end widgets Ccunters -->

                            <!--  widget events  -->
                                @can('view events')
                                    <div id="events" class="block block-rounded">
                                        <div class="block-header bg-corporate">
                                            <h3 class="block-title">
                                                {!! trans('dashboard.widgets_events') !!}
                                            </h3>
                                            <div class="block-options">
                                                    @if( $displayEvent )
                                                        <a href="{{ route('events.list' ) }}">
                                                            <i class="fa-regular fa-calendar-days fa-2x btn btn-rounded btn-block-option"></i>
                                                        </a>
                                                    @else
                                                        <i class="fa-regular fa-calendar-days fa-2x btn btn-rounded btn-block-option"></i>
                                                    @endif
                                            </div>
                                        </div>
                                        <div id="event-attendances" class="block-content block-content-full">
                                            @if(($events) && @count($events)>0)
                                                @php $lastElement = $events->last(); @endphp
                                                @foreach($events as $event)
                                                    <div class="row event">
                                                        <div class="col-12 col-sm-5 text-center align-middle event-name pl-10">
                                                            <a class="btn btn-alt-primary btn-block " href="{{ route('dashboard', $event->getId() ) }}">
                                                                <span>{{ $event->getName() }}</span>
                                                            </a>
                                                        </div>
                                                        <div class="col-6 col-sm-4 text-center align-middle event-buttons">
                                                            <a href="{{ route('event.attendance', $event->getId() ) }}">
                                                                <span class="btn btn-success">{!! $event->countAttenders()['ok'] !!}</span>
                                                                <span class="btn btn-danger">{!! $event->countAttenders()['nok'] !!}</span>
                                                                <span class="btn btn-warning">{!! $event->countAttenders()['unknown'] !!}</span>
                                                            </a>
                                                        </div>
                                                        <div class="col-4 col-sm-2 text-center event-date">
                                                            <span class="align-middle">{{ \App\Helpers\Humans::parseDate($event->getStartDate(),true) }}</span>
                                                        </div>
                                                        <div class="col-2 col-sm-1 text-center event-reminder pl-10 pr-0">
                                                            <span class="align-middle"><i class="sendReminder fa-solid fa-bell fa-2x text-primary" data-id_event="{!! $event->getId() !!}"data-toggle="tooltip" data-placement="right" title="{!! trans('notifications.tooltip_send_reminder') !!}"></i></span>
                                                        </div>
                                                    </div>
                                                    @if ($event !== $lastElement)
                                                        <hr>
                                                    @endif
                                                @endforeach
                                            @else
                                                <div>
                                                    {!! trans('dashboard.nothing_to_show') !!}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endcan
                            <!--  end widget sevents  -->

                        </div>
                    </div>
                <!-- end COLUM #2 -->

        </div>
        <!-- END Page Content -->
        <!-- START - Modal -->
        <div class="modal fade" id="modalSendReminder" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <div class="modal-content" id="modelSendReminderContent">
                    <!-- MODAL CONTENT -->
                    @include('notifications.modals.modal-send-reminder')
                </div>
            </div>
        </div>
        @include('modals.modal-success')
        @include('modals.modal-error')
        @include('events.modals.modal-attach-board')
        <!-- END - Modal -->
    </main>
    <!-- END Main Container -->

@endauth



@endsection

@section('js')

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

            let eventId = {{ ($displayEvent) ? $displayEvent->getId() : null }};

            @if(Auth::user()->getColla()->getConfig()->getBoardsEnabled())

                $('#pinyes-icon.empty-boards').on('click', function (e){

                    e.preventDefault();

                    let url = "{{ route('event.board.attach', ['event' => ':eventId']) }}";
                    url = url.replace(':eventId', eventId);

                    $('#formAttachBoard').attr('action', url);
                    $('#modalAttachBoard').modal('show');
                });
            @endif

            function sendReminder(id_event){

                var message = document.getElementById("message");
                var messageValue = message.value;

                let url = "{{ route('event.attendance.notify_missing', ['event' => ':event']) }}";
                url = url.replace(':event', id_event);

                $.post(url,
                        {
                            customMessage: messageValue,
                        }
                    )
                .then(function(result, status){
                    $('#modalSuccess').modal('show');
                 })
                .fail(function(result, status){
                    $('#modalError').modal('show');
                });

            }

            $('.sendReminder').on('click', function ()
            {
                $(this).tooltip('hide');
                let event_id = $(this).data().id_event;
                $('#modalSendReminder .btn-send-reminder').attr("data-id_event", event_id);
                $('#modalSendReminder').modal('show');
            });

            $("#modalSendReminder .btn-send-reminder").on('click', function(){
                sendReminder($(this).attr("data-id_event"));
            });

    });
</script>
@endsection

