@extends('template.main')

@if($type==\App\Enums\TypeTags::CASTELLERS && Auth::user()->getColla()->getConfig()->getBoardsEnabled())
    @section('title', trans('general.tags_and_positions'))
@else
    @section('title', trans('general.tags'))
@endif

@section('css_after')
    <link href="{{ asset('css/modals/action_buttons_datatables.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title">
            <b>
            @if($type === \App\Enums\TypeTags::CASTELLERS && Auth::user()->getColla()->getConfig()->getBoardsEnabled())
                {!! trans('general.tags_and_positions') !!}
            @elseif($type === \App\Enums\TypeTags::CASTELLERS)
                    {!! trans('general.tags') !!}
            @elseif($type === \App\Enums\TypeTags::EVENTS)
                {!! trans('tag.event_tags') !!}
            @elseif($type === \App\Enums\TypeTags::ATTENDANCE)
                {!! trans('attendance.attendance_answers') !!}
            @elseif($type === \App\Enums\TypeTags::BOARDS)
                {!! trans('boards.bases') !!}
            @endif
            </b>
        </h3>
    </div>
    <div class="block-content block-content-full">
        @if($type === \App\Enums\TypeTags::CASTELLERS)
            @can('edit BBDD')
                <div class="row">
                    <div class="col-md-12 text-right" style="margin-bottom: 20px;">
                        <button class="btn btn-primary btn-add-tag"><i class="fa fa-plus-circle"></i> {!! trans('tag.add_tag') !!}</button>
                    </div>
                </div>
            @endcan
        @elseif($type === \App\Enums\TypeTags::EVENTS)
            @can('edit events')
                <div class="row">
                    <div class="col-md-12 text-right" style="margin-bottom: 20px;">
                        <button class="btn btn-primary btn-add-event-tag"><i class="fa fa-plus-circle"></i> {!! trans('tag.add_tag') !!}</button>
                    </div>
                </div>
            @endcan
        @elseif($type === \App\Enums\TypeTags::ATTENDANCE)
            @can('edit events')
                <div class="row">
                    <div class="col-md-12 text-right" style="margin-bottom: 20px;">
                        <button class="btn btn-primary btn-add-attendance-tag"><i class="fa fa-plus-circle"></i> {!! trans('tag.add_attendance_tag') !!}</button>
                    </div>
                </div>
            @endcan
        @elseif($type === \App\Enums\TypeTags::BOARDS)
            @can('edit boards')
                <div class="row">
                    <div class="col-md-12 text-right" style="margin-bottom: 20px;">
                        <button class="btn btn-primary btn-add-board-tag"><i class="fa fa-plus-circle"></i> {!! trans('boards.add_base') !!}</button>
                    </div>
                </div>
            @endcan
        @endif

        <div class="row">
            <div @if(isset($tag_tags_groups)) class="col-md-12" @else class="col-md-8 offset-2" @endif>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>@if($type === \App\Enums\TypeTags::BOARDS){!! trans('boards.base') !!}@else{!! trans('tag.tag') !!}@endif</th>
                                @if(isset($tag_tags_groups))<th colspan="2">{!! trans('casteller.group') !!}</th>@endif
                                <th class="text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $editPrivileges = false;
                                $text = 'tag.not_tags';
                            @endphp
                                @switch($type)
                                    @case(\App\Enums\TypeTags::CASTELLERS)
                                        @php
                                            $text = 'tag.not_castellers_tags';
                                        @endphp
                                        @can('edit BBDD')
                                            @php
                                                $editPrivileges = true;
                                            @endphp
                                        @endcan
                                    @break

                                    @case(\App\Enums\TypeTags::EVENTS)
                                        @php
                                            $text = 'tag.not_events_tags';
                                        @endphp
                                        @can('edit events')
                                            @php
                                                $editPrivileges = true;
                                            @endphp
                                        @endcan
                                    @break

                                    @case(\App\Enums\TypeTags::ATTENDANCE)
                                        @php
                                            $text = 'tag.not_attendance_tags';
                                        @endphp
                                        @can('edit events')
                                            @php
                                                $editPrivileges = true;
                                            @endphp
                                        @endcan
                                    @break

                                    @case(\App\Enums\TypeTags::BOARDS)
                                        @php
                                            $text = 'tag.not_tags';
                                        @endphp
                                        @can('edit boards')
                                            @php
                                                $editPrivileges = true;
                                            @endphp
                                        @endcan
                                    @break

                                @endswitch

                            @if(count($tags)<1)

                                <tr>
                                    <td colspan="4" class="text-info text-center h5">{!! trans($text) !!} @php $text @endphp</td>
                                </tr>

                            @else

                                @foreach($tags as $tag)
                                    <tr>
                                        <td><h5 class="text-primary">{!! $tag->getName() !!}</h5></td>
                                        @if(isset($tags_groups))
                                        <td>
                                            <select class="form-control group-tag" name="group" id="group" data-id_tag="{!! $tag->getId() !!}">
                                                <option value="1" @if($tag->getGroup() === '1') selected @endif>{!! trans('casteller.group') !!} 1</option>
                                                <option value="2" @if($tag->getGroup() === '2') selected @endif>{!! trans('casteller.group') !!} 2</option>
                                                <option value="3" @if($tag->getGroup() === '3') selected @endif>{!! trans('casteller.group') !!} 3</option>
                                                <option value="4" @if($tag->getGroup() === '4') selected @endif>{!! trans('casteller.group') !!} 4</option>
                                                <option value="5" @if($tag->getGroup() === '5') selected @endif>{!! trans('casteller.group') !!} 5</option>
                                                <option value="6" @if($tag->getGroup() === '6') selected @endif>{!! trans('casteller.group') !!} 6</option>
                                            </select>
                                        </td>

                                        <td><div class="spinner-border" id="{!! $tag->id_tag !!}" style="display: none;" role="status"><span class="sr-only">Loading...</span></div></td>
                                        @endif
                                        <td class="text-right">
                                            @if($editPrivileges)
                                                @if($tag->getType() !== \App\Enums\TypeTags::POSITIONS)<button class="btn btn-warning btn-edit-tag btn-action" data-id_tag="{!! $tag->getId() !!}"><i class="fa fa-pencil"></i></button>@endif
                                                @if($tag->isUsed())
                                                    <button class="btn btn-danger btn-delete-used-tag btn-action" data-id_tag="{!! $tag->getId() !!}"><i class="fa fa-times"></i></button>
                                                @else
                                                    <button class="btn btn-danger btn-delete-tag btn-action" data-id_tag="{!! $tag->getId() !!}"><i class="fa fa-times"></i></button>
                                                @endif
                                             @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

            @if($type === \App\Enums\TypeTags::CASTELLERS && Auth::user()->getColla()->getConfig()->getBoardsEnabled())
                @can('edit BBDD')
                    <hr>
                    <div class="row">
                        <div class="col-md-12 text-right" style="margin-bottom: 20px;">
                            <button class="btn btn-primary btn-add-position-tag"><i class="fa fa-plus-circle"></i> {!! trans('casteller.add_position') !!}</button>
                        </div>
                    </div>
                @endcan
                <div class="row">
                    <div class="col-md-8 offset-2">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>{!! trans('casteller.position') !!}</th>
                                <th class="text-right"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($positions)<1)
                                <tr>
                                    <td colspan="2" class="text-info text-center h5">{!! trans('tag.not_position_tags') !!}</td>
                                </tr>
                            @else
                                @foreach($positions as $position)
                                    <tr>
                                        <td><h5 class="text-primary">{!! $position->getName() !!}</h5></td>
                                        <td class="text-right">
                                            @if($editPrivileges)
                                                @if($position->getType() !== \App\Enums\TypeTags::POSITIONS)<button class="btn btn-warning btn-edit-tag" data-id_tag="{!! $position->getId() !!}"><i class="fa fa-pencil"></i></button>@endif
                                                @if($position->isUsed())
                                                    <button class="btn btn-danger btn-action" data-toggle="tooltip" title="{!! trans('tag.btn_delete_dissabled') !!}" data-placement="top" disabled><i class="fa fa-times"></i></button>
                                                @else
                                                    <button class="btn btn-danger btn-delete-position-tag btn-action" data-id_tag="{!! $position->getId() !!}"><i class="fa fa-times"></i></button>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            @endif

    </div>
</div>

<!-- START - Modal Edit Tag -->
<div class="modal fade" id="modalEditTag" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <div class="modal-content" id="modalEditTagContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>
<!-- END - Modal Update Tag -->

<!-- START - Modal Add Tag -->
<div class="modal fade" id="modalAddTag" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <div class="modal-content" id="modalAddTagContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>
<!-- END - Modal Add Tag -->

<!-- START - MODAL DELETE TAG -->
<div class="modal fade" id="modalDelTag" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-popin" role="document">
        <div class="modal-content">
            {!! Form::open(array('url' => 'n', 'method' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'fromDelTag')) !!}
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">{!! trans('tag.del_tag') !!}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>

                <div class="block-content text-center">
                    <i class="fa fa-warning" style="font-size: 46px;"></i>
                    <h3 class="semibold modal-title text-danger">{!! trans('general.caution') !!}</h3>
                    @if($type==App\Enums\TypeTags::ATTENDANCE)
                        <p class="text-muted">{!! trans('attendance.del_attendance_answer_warning') !!}</p>
                    @else
                        <p class="text-muted" id="text-delete">{!! trans('tag.del_tag_warning') !!}</p>
                    @endif

                </div>
            </div>
            <div class="modal-footer">
                {!! Form::submit(trans('general.delete') , array('class' => 'btn btn-danger')) !!}
                <button type="button" class="btn  btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
            </div>
            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!--/ END - MODAL DELETE TAG-->

<!-- START - MODAL DELETE USED TAG -->
<div class="modal fade" id="modalDelUsedTag" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-popin" role="document">
        <div class="modal-content">
            {!! Form::open(array('url' => 'n', 'method' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'fromDelUsedTag')) !!}
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">{!! trans('tag.del_tag') !!}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>

                <div class="block-content text-center">
                    <i class="fa fa-warning" style="font-size: 46px;"></i>
                    <h3 class="semibold modal-title text-danger">{!! trans('general.caution') !!}</h3>
                    @if($type==App\Enums\TypeTags::ATTENDANCE)
                        <p class="text-muted">{!! trans('attendance.del_attendance_answer_warning') !!}</p>
                    @else
                        <p class="text-muted" id="text-delete">{!! trans('tag.del_used_tag_warning') !!}</p>
                    @endif

                </div>
            </div>
            <div class="modal-footer">
                {!! Form::submit(trans('general.delete') , array('class' => 'btn btn-danger')) !!}
                <button type="button" class="btn  btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
            </div>
            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!--/ END - MODAL DELETE USED TAG-->

@endsection

@section('js')
    <script src="{!! asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') !!}"></script>
    <script>
        $(function ()
        {
            function notification()
            {
                Codebase.helpers('notify', {
                    align: 'center',             // 'right', 'left', 'center'
                    from: 'top',                // 'top', 'bottom'
                    type: 'success',               // 'info', 'success', 'warning', 'danger'
                    icon: 'fa fa-info mr-5',    // Icon class
                    message: 'Your message!'
                });
            }

            @can('edit BBDD')

                $('.group-tag').on('change', function () {

                    let id_tag = $(this).data().id_tag;

                    $('#'+id_tag).toggle();

                    let group = $(this).val();

                    let url = "{{ route('castellers.tags.toggle-group', ['tag' => ':id_tag', 'group' => ':group']) }}";

                    url = url.replace(':id_tag',id_tag);
                    url = url.replace(':group',group);

                    $.get(url, function (data) {
                        console.log(data);
                        if(data) {
                            $('#'+id_tag).toggle();
                        }
                    } );

                });

                $('.btn-add-tag').on('click', function (event)
                {
                    $('#modalAddTag').modal('show');

                    $('#modalAddTagContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                    $.get( "{{ route('castellers.tags.add-modal') }}", function( data ) {
                        $('#modalAddTagContent').html( data );
                    });
                });

                $(".btn-edit-tag").on('click', function (event)
                {
                    let id_tag = $(this).data().id_tag;

                    $('#modalAddTag').modal('show');

                    $('#modalAddTagContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                    let url = "{{ route('castellers.tags.edit-tag-modal', ':id_tag') }}";
                    url = url.replace(':id_tag',id_tag);

                    $.get( url, function( data ) {
                        $('#modalAddTagContent').html( data );
                    });
                });

                $(".btn-delete-tag").on('click', function (event)
                {
                    let id_tag = $(this).data().id_tag;
                    $('#modalDelTag').modal('show');

                    let url = "{{ route('castellers.tags.destroy', ':id_tag') }}";
                    url = url.replace(':id_tag',id_tag);

                    $('#fromDelTag').attr('action', url);
                });

                $(".btn-delete-used-tag").on('click', function (event)
                {
                    let id_tag = $(this).data().id_tag;
                    $('#modalDelUsedTag').modal('show');

                    let url = "{{ route('castellers.tags.destroy', ':id_tag') }}";
                    url = url.replace(':id_tag',id_tag);

                    $('#fromDelUsedTag').attr('action', url);
                });

                $(".btn-delete-position-tag").on('click', function (event)
                {
                    let id_tag = $(this).data().id_tag;
                    $('#text-delete').html('{!! trans('casteller.del_position_warning') !!}');
                    $('#modalDelTag').modal('show');



                    let url = "{{ route('castellers.tags.destroy', ':id_tag') }}";
                    url = url.replace(':id_tag',id_tag);

                    $('#fromDelTag').attr('action', url);
                });

                $('.btn-add-position-tag').on('click', function (event)
                {
                    $('#modalAddTag').modal('show');

                    $('#modalAddTagContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                    $.get( "{{ route('castellers.tags.add-modal', 'POSITION') }}", function( data ) {
                        $('#modalAddTagContent').html( data );
                    });
                });

            @endcan

            @can('edit events')

                $('.btn-add-event-tag').on('click', function (event)
                {
                    $('#modalAddTag').modal('show');

                    $('#modalAddTagContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                    $.get( "{{ route('castellers.tags.add-modal', $type) }}", function( data ) {
                        $('#modalAddTagContent').html( data );
                    });
                });

                $('.btn-add-attendance-tag').on('click', function (event)
                {
                    $('#modalAddTag').modal('show');

                    $('#modalAddTagContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                    $.get( "{{ route('events.answers.add-modal', App\Enums\TypeTags::ATTENDANCE) }}", function( data ) {
                        $('#modalAddTagContent').html( data );
                    });
                });
            @endcan

            @can('edit boards')

                $('.btn-add-board-tag').on('click', function (event)
                {
                    $('#modalAddTag').modal('show');

                    $('#modalAddTagContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                    $.get( "{{ route('castellers.tags.add-modal', $type) }}", function( data ) {
                        $('#modalAddTagContent').html( data );
                    });
                });

            @endcan
        });
    </script>
@endsection
