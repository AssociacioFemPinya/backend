
@push('css_before')
<style>
    #totalCastellers{
        font-size: 24px;
    }
    #resetFilter{
        cursor:pointer;
    }
</style>
@endpush

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
            <option value="all" selected>{!! trans('tag.all_tags') !!}</option>
            <option value="" disabled>{!! trans('general.tags') !!}</option>
            @foreach($tags as $tag)
                <option value="{!! $tag->getId() !!}">{!! $tag->getName() !!}</option>
            @endforeach
            @if(Auth::user()->getColla()->getConfig()->getBoardsEnabled())
                <option value="" disabled>{!! trans('casteller.positions') !!}</option>
                @foreach($positions as $position)
                    <option value="{!! $position->getId() !!}">{!! $position->getName() !!}</option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="col-md-2">
        <select name="status[]" id="status" class="selectize2 form-control">
            <option value="{{ App\Enums\CastellersStatusEnum::All()->value() }}" selected>{!! trans('casteller.everybody') !!}</option>
            @foreach ($statuses as $num => $status)
                <option value="{{ $num }}">{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div id="filter-icons" class="col-md-4 text-left ">
        <i id="resetFilter" class="fa-solid fa-2x fa-arrow-rotate-right pr-20 text-primary" data-toggle="tooltip" data-placement="right" title="{!! trans('general.tooltip_reset_filter') !!}"></i>
        <span id="totalCastellers"></span>  <i class="fa-solid fa-2x fa-users"></i>
    </div>

