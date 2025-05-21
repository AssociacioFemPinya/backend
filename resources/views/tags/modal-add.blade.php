
<div class="block block-themed block-transparent mb-0">
    <div class="block-header bg-primary-dark">
        <h3 class="block-title">
            @if(isset($tag))
                @if($tag->type=='CASTELLERS')
                    {!! trans('tag.update_tag') !!}
                @elseif($tag->type=='EVENTS')
                    {!! trans('tag.update_event_tag') !!}
                @elseif($tag->type=='ATTENDANCE')
                    {!! trans('tag.update_attendance_tag') !!}
                @elseif($type=='POSITION')
                    {!! trans('casteller.update_position') !!}
                @endif
            @else
                @if($type=='CASTELLERS')
                    {!! trans('tag.add_tag') !!}
                @elseif($type=='EVENTS')
                    {!! trans('tag.add_tag_event') !!}
                @elseif($type=='ATTENDANCE')
                    {!! trans('attendance.attendance_answers') !!}
                @elseif($type=='POSITION')
                    {!! trans('casteller.add_position') !!}
                @endif
            @endif
        </h3>
        <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                <i class="si si-close"></i>
            </button>
        </div>
    </div>
    @if(isset($tag))
        {!! Form::open(array('id' => 'FormUpdateTag', 'url' => route('castellers.tags.update', $tag->id_tag), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
    @else
        @if($type=='CASTELLERS')
            {!! Form::open(array('id' => 'FormAddTag', 'url' => route('castellers.tags.add'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
        @elseif($type=='EVENTS')
            {!! Form::open(array('id' => 'FormAddTag', 'url' => route('events.tags.add'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
        @elseif($type=='ATTENDANCE')
            {!! Form::open(array('id' => 'FormAddTag', 'url' => route('events.answers.add'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
        @elseif($type=='BOARDS')
            {!! Form::open(array('id' => 'FormAddTag', 'url' => route('boards.tags.add'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
        @elseif($type=='POSITION')
            {!! Form::open(array('id' => 'FormAddTag', 'url' => route('castellers.position.add'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
        @endif
    @endif
    <div class="block-content">
        <div class="row form-group">
            @if(isset($tags_groups))<div class="col-md-6">@else<div class="col-md-12">@endif
                <label class="control-label">{!! trans('general.name') !!}</label>
                <input type="text" class="form-control" id="name" name="name" value="@if(isset($tag)){!! $tag->name !!}@endif" minlength="2" required>
            </div>
            @if($type=='CASTELLERS')
                <div class="col-md-6">
                    <label class="control-label">{!! trans('casteller.group') !!}</label>
                    <select class="form-control" name="group" id="group">
                        <option value="1" @if(isset($tag) && $tag->group=='1') selected @endif>{!! trans('casteller.group') !!} 1</option>
                        <option value="2" @if(isset($tag) && $tag->group=='2') selected @endif>{!! trans('casteller.group') !!} 2</option>
                        <option value="3" @if(isset($tag) && $tag->group=='3') selected @endif>{!! trans('casteller.group') !!} 3</option>
                        <option value="4" @if(isset($tag) && $tag->group=='4') selected @endif>{!! trans('casteller.group') !!} 4</option>
                        <option value="5" @if(isset($tag) && $tag->group=='5') selected @endif>{!! trans('casteller.group') !!} 5</option>
                    </select>
                </div>
            @endif
        </div>
    </div>
</div>
<div class="modal-footer">
    @if(isset($tag))
        <button type="submit" form="FormUpdateTag" class="btn btn-alt-primary">{!! trans('general.update') !!}</button>
    @else
        <button type="submit" form="FormAddTag" class="btn btn-alt-primary">{!! trans('general.save') !!}</button>
    @endif
    <a type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</a>
</div>
{!! Form::close() !!}

