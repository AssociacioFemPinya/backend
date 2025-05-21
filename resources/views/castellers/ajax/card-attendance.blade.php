<div id="attendance_tab" class="block">
    <div class="block-content block-content-full">
        <div class="row">
            <div class="col-md-1">
                <label class="control-label" style="padding-top: 5px;">
                    {!! trans('general.filter') !!}
                </label>
            </div>

            <div class="col-md-1">
                <select name="filter_search_type" id="filter_search_type" class="selectize2 form-control">
                    <option value="AND">AND</option>
                    <option value="OR" selected>OR</option>
                </select>
            </div>
            <div class="col-md-4">
                <select name="tags[]" id="tags" class="selectize2 form-control" multiple>
                    <option value="all" selected>{!! trans('event.all') !!}</option>
                    @foreach($tags as $tag)
                        <option value="{!! $tag->getId() !!}">{!! $tag->getName() !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="tags_event_type[]" id="tags_event_type" class=" form-control">
                    <option value=0 selected>{!! trans('event.all') !!}</option>
                    @foreach($tags_event_type as $index => $value)
                        <option value="{!! $index !!}">{!! $value !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="search_period" id="search_period" class=" form-control">
                    @foreach($periods as $period)
                        <option value="{!! $period->getId() !!}" @if(isset($currentPeriod) && ($period == $currentPeriod)) selected @endif>{!! $period->getName() !!}</option>
                    @endforeach
                    <option value="0" @if(!isset($currentPeriod)) selected @endif>{!! trans('event.all') !!}</option>
                </select>
            </div>
            <div id="filter-icons" class="col-md-2 text-left ">
                <i id="resetFilter" class="fa-solid fa-2x fa-arrow-rotate-right pr-20 text-primary" data-toggle="tooltip" data-placement="right" title="{!! trans('general.tooltip_reset_filter') !!}"></i>
            </div>
        </div>
    </div>
</div>

<div class="block">
    <div class="block-header ">
        <div class="block-title">
            <h3 class="block-title">{!! trans('event.upcoming_events') !!}</h3>
        </div>
    </div>
    <div class="block-content">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped table-vcenter" style="width: 100%;" id="events_upcoming">
                <thead>
                <tr>
                    <th>{!! trans('general.name') !!}</th>
                    <th>{!! trans('general.type') !!}</th>
                    <th>{!! trans('general.tags') !!}</th>
                    <th>{!! trans('general.date') !!}</th>
                    <th><i class="fa-solid fa-check" style="font-size: 22px;"></i></th>
                    <th><i class="fa-solid fa-check-double" style="font-size: 22px;"></i></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="block">
    <div class="block-header ">
        <div class="block-title">
            <h3 class="block-title">{!! trans('event.past_events') !!}</h3>
        </div>
    </div>
    <div class="block-content">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped table-vcenter" style="width: 100%;" id="events_past">
                <thead>
                <tr>
                    <th>{!! trans('general.name') !!}</th>
                    <th>{!! trans('general.type') !!}</th>
                    <th>{!! trans('general.tags') !!}</th>
                    <th>{!! trans('general.date') !!}</th>
                    <th><i class="fa-solid fa-check" style="font-size: 22px;"></i></th>
                    <th><i class="fa-solid fa-check-double" style="font-size: 22px;"></i></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>




