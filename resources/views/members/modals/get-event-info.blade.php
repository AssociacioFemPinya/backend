<div class="container-fluid">
    <!-- Close button -->
    <div class="block-options d-flex justify-content-end"> <!-- Added justify-content-end class -->
        <button type="button" class="btn btn-lg btn-block-option" data-dismiss="modal" aria-label="Close">
            <i class="si si-close"></i>
        </button>
    </div>
    <!-- Close button -->

    <!-- Event Header -->
    <div class="row mt-4">
        <div class="col-md-12">
            <h1 class="text-center display-4" style="border-bottom: 1px solid #ccc;">
                {!! $event->getName() !!}
            </h1>
        </div>
    </div>
    <!-- Event Header -->

    <!-- Attendances -->
    <div class="row mt-3">
        <div class="col-md-12">
            <h4 class="text-success text-center">
                <i class="bi bi-people"></i> {!! $event->countAttenders()['ok'] !!} {!! trans('event.attendees') !!}
            </h4>
        </div>
    </div>
    <!-- Attendances -->

    <!-- Event Info with light blue background and margin -->
    <div class="row mt-4 info-section">
        <div class="col-md-6">
            <p class="text-muted">
                <i class="bi bi-calendar2-check"></i> &nbsp; {!! App\Helpers\Humans::parseDate($event->getStartDate()) !!}<br>
                <i class="bi bi-clock-history"></i> &nbsp; {!! round($event->getDuration()/60,1) !!} {!! trans('event.hours') !!}
            </p>
        </div>
        <div class="col-md-6">
            <p class="text-muted">
                @if ($event->getLocationLink())
                <i class="bi bi-geo-alt"></i> &nbsp; <a href="{!! $event->getLocationLink() !!}">{!! $event->getAddress() !!}</a>
                @else
                <i class="bi bi-geo-alt"></i> &nbsp; {!! $event->getAddress() !!}
                @endif
            </p>
        </div>
    </div>
    <!-- Event Info with light blue background and margin -->

    <!-- Event Description with gray background and margin -->
    @if ($event->getComments() != null)
    <div class="row description-section">
        <div class="col-md-12">
            <pre><p>{!! $event->getComments() !!}</p></pre>
        </div>
    </div>
    @endif
    <!-- Event Description with gray background and margin -->
</div>
