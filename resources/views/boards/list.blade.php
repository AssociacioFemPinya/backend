@extends('template.main')

@section('title', trans('general.templates'))
@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
@endsection
@section('css_after')
    <style>
        .result_pinya div {
            position: absolute;
            text-align: center;
            line-height: 28px;
            display: block;
            overflow: hidden;
            font-size: 11.5px;
            width: 72px;
            height: 31.6px;
            font-family: Helvetica, Verdana, sans-serif;
            color: black;
        }

        .radius_1 {border-radius: 5px;}
        .radius_2 {border-radius: 10px;}
        .radius_3 {border-radius: 15px;}

        .border_1{ border: 1px solid grey;}
        .border_2{ border: 2px solid grey;}
        .border_3{ border: 3px solid grey;}
        .border_4{ border: 4px solid grey;}
        .border_5{ border: 1px dotted grey;}
        .border_6{ border: 2px dotted grey;}
        .border_7{ border: 3px dotted grey;}
        .border_8{ border: 4px dotted grey;}
        .border_9{ border: 1px double grey;}
        .border_10{ border: 2px double grey;}
        .border_11{ border: 3px double grey;}
        .border_12{ border: 4px double grey;}


        .bg_color_1{background-color: #fadbd8;}
        .bg_color_2{background-color: #f5b7b1;}
        .bg_color_3{background-color: #f1948a;}
        .bg_color_4{background-color: #a9dfbf;}
        .bg_color_5{background-color: #52be80;}
        .bg_color_6{background-color: #27ae60;}
        .bg_color_7{background-color: #f9e79f;}
        .bg_color_8{background-color: #f4d03f;}
        .bg_color_9{background-color: #FFD700;}
        .bg_color_10{background-color: #f5cba7;}
        .bg_color_11{background-color: #f0b27a;}
        .bg_color_12{background-color: #eb984e;}
        .bg_color_13{background-color: #81d4fa;}
        .bg_color_14{background-color: #29b6f6;}
        .bg_color_15{background-color: #039be5;}

        .shadow_0 {box-shadow:  none; }
        .shadow_1 {box-shadow: 2px 2px 2px gray; }
        .shadow_2 {box-shadow:  4px 4px 4px gray; }
        .shadow_3 {box-shadow:  6px 6px 6px gray; }

        .danger-text {
            border: 2px solid red;
            border-radius: 7px;
            padding: 4px 5px;
            margin: 7px 0;
            color:red;
            font-weight: 600;
        }

    </style>
    <link href="{{ asset('css/modals/action_buttons_datatables.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="block">
    <div class="block-header block-header-default">
        <div class="block-title">
            <h3 class="block-title"><b>{!! trans('general.templates') !!}</b></h3>
        </div>
        <div class="block-options">

            <button class="btn btn-primary btn-add-board" href="{!! route('boards.add') !!}"><i class="si si-map"></i> {!! trans('boards.add_template') !!}</button>

        </div>
    </div>
    <div class="block-content block-content-full">
        <div class="row">

            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered table-vcenter" id="boards" width="100%">
                        <thead>
                            <tr>
                                <th>{!! trans('general.name') !!}</th>
                                <th>{!! trans('boards.type') !!}</th>
                                <th>{!! trans('boards.base') !!}</th>
                                <th>{!! trans('boards.share') !!}</th>
                                <th>{!! trans('boards.added_to') !!}</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($boards as $board)
                            <tr>
                                <td>{!! $board->name !!}</td>
                                <td>
                                    @if($board->type==='PINYA')
                                        {!! trans('boards.pinya') !!}
                                    @elseif($board->type==='FOLRE')
                                        {!! trans('boards.folre') !!}
                                    @elseif($board->type==='MANILLES')
                                        {!! trans('boards.manilles') !!}
                                    @elseif($board->type==='PUNTALS')
                                        {!! trans('boards.puntals') !!}
                                    @endif
                                </td>
                                <td>
                                    @foreach ($board->tags() as $tag)
                                        <span style="margin-left: 3px; margin-bottom: 3px;">{!! $tag->name !!}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <input type="checkbox" data-linkedBoardId={!! (string) $board->getId() !!} {!! $board->is_public? "checked":"" !!} class="form-check-input isPublicCheck">
                                    </div>
                                </td>
                                <td class="d-flex justify-content-center">{!! date('d/m/Y', strtotime($board->created_at)) !!}</td>
                                <td>
                                    <button class="btn btn-info btn-preview btn-action" data-board="{{ $board->id_board }}"><i class="fa fa-eye"></i></button>

                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-warning  btn-action dropdown-toggle" id="btnGroupDrop1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-pencil"></i></button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                            <a class="dropdown-item" href="{{ route('boards.add-map', ['board' => $board->getId(), 'map' => \App\Enums\BasesEnum::PINYA]) }}">
                                                {!! trans('boards.edit_base', ['BASE' => \App\Enums\BasesEnum::PINYA]) !!}
                                            </a>
                                            @if($board->hasFolre())
                                                <a class="dropdown-item" href="{{ route('boards.add-map', ['board' => $board->getId(), 'map' => \App\Enums\BasesEnum::FOLRE]) }}">
                                                    {!! trans('boards.edit_base', ['BASE' => \App\Enums\BasesEnum::FOLRE]) !!}
                                                </a>
                                            @endif
                                            @if($board->hasManilles())
                                                <a class="dropdown-item" href="{{ route('boards.add-map', ['board' => $board->getId(), 'map' => \App\Enums\BasesEnum::MANILLES]) }}">
                                                    {!! trans('boards.edit_base', ['BASE' => \App\Enums\BasesEnum::MANILLES]) !!}
                                                </a>
                                            @endif
                                            @if($board->hasPuntals())
                                                <a class="dropdown-item" href="{{ route('boards.add-map', ['board' => $board->getId(), 'map' => \App\Enums\BasesEnum::PUNTALS]) }}">
                                                    {!! trans('boards.edit_base', ['BASE' => \App\Enums\BasesEnum::PUNTALS]) !!}
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <button class="btn btn-danger btn-delete-board btn-action" data-id_board="{{ $board->getId() }}"><i class="fa fa-trash-o"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- START - Modal long -->
<div class="modal fade" id="modalLong" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
        <div class="modal-content" id="modalLongContent">
            <!-- MODAL CONTENT -->
            <div class="col-md-12 text-center">
                <div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>
            </div>
        </div>
    </div>
</div>
<!-- END - Modal Add Board -->

<!-- START - Modal long -->
<div class="modal fade" id="modalAddBoard" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
        <div class="modal-content" id="modalAddBoardContent">
            <!-- MODAL CONTENT -->
            @include('boards.modals.modal-add')
        </div>
    </div>
</div>
<!-- END - Modal long -->

<!-- START - Modal import -->
<div class="modal fade" id="modalImportBoard" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
        <div class="modal-content" id="modalImportBoardContent">
            <!-- MODAL CONTENT -->
            @include('boards.modals.modal-import')
        </div>
    </div>
</div>
<!-- END - Modal import -->

<!-- START - MODAL DELETE -->
<div class="modal fade" id="modalDelBoard" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-popin" role="document">
        <div class="modal-content">
            {!! Form::open(array('url' => 'n', 'method' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'formDelBoard')) !!}
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">{!! trans('boards.del_board') !!}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content text-center">
                    <i class="fa fa-warning" style="font-size: 46px;"></i>
                    <h3 class="semibold modal-title text-danger">{!! trans('general.caution') !!}</h3>
                    <p class="text-muted">{!! trans('boards.del_board_warning') !!}</p>
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
<!--/ END - MODAL DELETE -->
@endsection

@section('js')
<script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

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
        $("#boards").DataTable({
            "language": {!! trans('datatables.translation') !!},
            "stateSave": true,
            "stateDuration": -1,
            "ordering":'true',
            "order": [0, 'asc'],
            "columns": [
                { "data": "name", "name": "name" },
                { "data": "type", "name": "type"},
                { "data": "base", "name": "base" },
                { "data": "isPubic", "name": "isPublic" },
                { "data": "added", "name": "added" },
                { "data": "btns", "name": "btns", "orderable": false },

            ],

            "columnDefs": [
                { "width": "30%", "targets": 0 },
                { "width": "14%", "targets": 1 },
                { "width": "14%", "targets": 2 },
                { "width": "14%", "targets": 3 },
                { "width": "14%", "targets": 4 },
                { "width": "14%", "targets": 5 }
            ],
            "responsive": true
        });

        $('#boards').on('click','.btn-delete-board', function()
        {
            var id_board = $(this).data().id_board;

            var url = '{{ route('boards.destroy', ['board' => ':id_board']) }}';
            url = url.replace(':id_board', id_board)

            $('#formDelBoard').attr('action', url);
            $('#modalDelBoard').modal('show');
        });

        $('.btn-add-board').on('click', function (event)
        {
            $('#modalAddBoard').modal('show');
        });

        $('#boards').on('change','.isPublicCheck', function()
        {
            $.post("{{ route('boards.setPublicBoard') }}",
            {
                id_board: $(this).attr('data-linkedBoardId'),
                is_public: $(this).prop('checked')? 1:0
            },
            function(data, status) {
                if(status === "success") {
                    console.log("Post successfully created!")
                }
            },
            "json");
            console.log('set to public');
        });

        $('#boards').on('click', '.btn-preview', function()
        {
            $('#modalLong').modal('show');

            var board = $(this).data().board;
            var url = "{{ route('boards.preview-board-ajax', ['board' => ':board']) }}";
            url = url.replace(':board', board)

            $.get( url, function( data ) {
                $('#modalLongContent').html( data );
            });
        });

    });


</script>

<script type="text/javascript"> // modal-import code
    $('#colla_id_select').on('input', function(event){
        console.dir(event.currentTarget.value);
        for (i of $('.colla-option')){
            i.classList.add('d-none');
        }
        for (i of $(`.colla-${event.currentTarget.value}`)){
            i.classList.remove('d-none');
        }
    });

    $('#pinya_id').on('input', function(event) {
        console.dir($(`#pinya_id option[value=${event.currentTarget.value}]`)[0]);
        console.dir($(`#pinya_id option[value=${event.currentTarget.value}]`)[0].getAttribute('data-pinyaUrl'));
        $('#previsualize_img')[0].src = $(`#pinya_id option[value=${event.currentTarget.value}]`)[0].getAttribute('data-pinyaUrl');
    });
</script>
@endsection
