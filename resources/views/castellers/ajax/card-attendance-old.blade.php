

<div class="row mb-15">
    Fins aqui.... ara espera $events['upcoming']
    @if (count($events['upcoming']) > 0)

        <div class="container-fluid agenda">
            <h5>{!! trans('event.upcoming_events') !!}</h5>
            <div class="table-responsive">
                <table class="table table-condensed table-bordered">
                    <thead>
                    <tr>
                        <th>{!! trans('general.date') !!}</th>
                        <th>{!! trans('general.name') !!}</th>
                        <th>{!! trans('general.name') !!}</th>
                        <th class="text-center" style="width: 7%;"><i style="font-size: 22px;" class="fa-solid fa-check"></i></th>
                        <th class="text-center" style="width: 7%;"><img style="width: 20px;" class="fa-solid fa-check-double"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($events['upcoming'] as $event)
                        <tr>
                            <td class="agenda-date" class="active">
                                <div class="row">
                                    <div class="col-4 pull-right text-right pr-0 dayofmonth">
                                        {{ \Carbon\Carbon::parse($event->getStartDate())->format('j') }}
                                    </div>
                                    <div class="col-8 text-left">
                                        <div class="dayofweek">{{ \Carbon\Carbon::parse($event->getStartDate())->format('l') }}</div>
                                        <div class="shortdate text-muted">{{ \Carbon\Carbon::parse($event->getStartDate())->format('F, Y') }}</div>
                                        <div class="shortdate text-muted">{{ \Carbon\Carbon::parse($event->getStartDate())->format('H.i') }}h</div>
                                    </div>
                                </div>
                            </td>
                            <td style="vertical-align: middle;" class="font-w600">
                                {{ $event->getName() }}
                            </td>
                            <td>
                                @if ( $attendance = $attendances->where('event_id', $event->getId())->get() )
                                    $attendance->getStatus();
                                @else
                                    {{ \App\Helpers\RenderHelper::fieldbutton('data-id_casteller', (string)$casteller->getId(), 'btn btn-secondary btn-status', 'fa-solid fa-question'); }}
                                @endif

                            </td>
                            <td>
                                @if ($event->attendance->status_verified == 'YES')
                                    <button class="btn btn-success btn-status-verified" data-id_event="{{ $event->id_event }}"><i class="fa-solid fa-check"></i></button>
                                @elseif ($event->attendance->status_verified == 'NO')
                                    <button class="btn btn-danger btn-status-verified" data-id_event="{{ $event->id_event }}"><i class="fa-solid fa-close"></i></button>
                                @else
                                    <button class="btn btn-secondary btn-status" data-id_event="{{ $event->id_event }}"><i class="fa-solid fa-question"></i></button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{--  @if (count($events['past']) > 0)
        <div class="container-fluid agenda mt-20">
            <h5>{!! trans('event.past_events') !!}</h5>
            <div class="table-responsive">
            <table class="table table-condensed table-bordered">
                <thead>
                <tr>
                    <th>{!! trans('general.date') !!}</th>
                    <th>{!! trans('general.name') !!}</th>
                    <th class="text-center" style="width: 7%;"><i style="font-size: 22px;" class="fa-solid fa-check"></i></th>
                    <th class="text-center" style="width: 7%;"><img style="width: 20px;" class="fa-solid fa-check-double"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($events['past'] as $event)
                    <tr>
                        <td class="agenda-date" class="active">
                            <div class="row">
                                <div class="col-4 pull-right text-right pr-0 dayofmonth">
                                    {{ Date::parse($event->start_date)->format('j') }}
                                </div>
                                <div class="col-8 text-left">
                                    <div class="dayofweek">{{ Date::parse($event->start_date)->format('l') }}</div>
                                    <div class="shortdate text-muted">{{ Date::parse($event->start_date)->format('F, Y') }}</div>
                                    <div class="shortdate text-muted">{{ Date::parse($event->start_date)->format('H.i') }}h</div>
                                </div>
                            </div>
                        </td>
                        <td style="vertical-align: middle;" class="font-w600">
                            {{ $event->name }}
                        </td>
                        <td>
                            @if ($event->attendance->status == 'YES')
                                <button class="btn btn-success btn-status" data-id_event="{{ $event->id_event }}"><i  class="fa-solid fa-check"></i></button>
                            @elseif ($event->attendance->status == 'NO')
                                <button class="btn btn-danger btn-status" data-id_event="{{ $event->id_event }}"><i class="fa-solid fa-close"></i></button>
                            @elseif($event->attendance->status == 'UNKNOWN')
                                <button class="btn btn-outline-warning btn-status" data-id_event="{{ $event->id_event }}"><i class="fa-solid fa-question"></i></button>
                            @else
                                <button class="btn btn-secondary btn-status" data-id_event="{{ $event->id_event }}"><i class="fa-solid fa-question"></i></button>
                            @endif
                        </td>
                        <td>
                            @if ($event->attendance->status_verified == 'YES')
                                <button class="btn btn-success btn-status-verified" data-id_event="{{ $event->id_event }}"><i class="fa-solid fa-check"></i></button>
                            @elseif ($event->attendance->status_verified == 'NO')
                                <button class="btn btn-danger btn-status-verified" data-id_event="{{ $event->id_event }}"><i class="fa-solid fa-close"></i></button>
                            @elseif($event->attendance->status_verified == 'UNKNOWN')
                                <button class="btn btn-outline-warning btn-status-verified" data-id_event="{{ $event->id_event }}"><i class="fa-solid fa-question"></i></button>
                            @else
                                <button class="btn btn-secondary btn-status-verified" data-id_event="{{ $event->id_event }}"><i class="fa-solid fa-question"></i></button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </div>
    @endif  --}}

</div>
