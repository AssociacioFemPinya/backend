@extends('template.main')

@section('title', trans('general.boards'))
@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

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

        .position-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            width: -webkit-fill-available;
        }
        .position-container li {
            margin-left: 20px;
        }

        .board-container {
            display:flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .board-container .container-body {
            height: -webkit-fill-available;
            display: grid;
            align-content: space-between;
        }
    </style>
@endsection

@section('content')
<div class="block">
    <div class="block-header block-header-default">
        <div class="block-title">
            <h3 class="block-title"><b>{!! trans('boards.biblio_title') !!}</b></h3>
        </div>
        <div class="block-options">

            <button class="btn btn-primary btn-add-board" href="{!! route('boards.add') !!}"><i class="si si-map"></i> {!! trans('boards.add_template') !!}</button>

        </div>
    </div>
    <div class="block-content block-content-full">
    <div class="album py-5 bg-light">


        <div class="ml-4 mt-4">
            <h3 class="jumbotron-heading">{!! trans('boards.own_positions') !!}</h3>
            <div class="position-container">
                @foreach ($colla_tags as $colla_tag)
                    <li>{!! $colla_tag->name !!}</li>
                @endforeach
            </div>
        </div>
        <hr class="my-4">

        <div class="container">

          <div class="row">
            <!-- START - plantilla -->
            @foreach($boards as $board)

                @php($id_board = $board->id_board)

                <div class="col-md-4 border-success board-container">

                    <div class="card-header">
                        <h3 class="jumbotron-heading">{!! $board->name !!}
                            <small class="text-muted">{!! $board->colla->getShortname() !!}</small>

                        </h3>

                    </div>
                    <div class="card mb-4 box-shadow container-body">
                            <!-- {--!! Form::open(array('id' => 'FormImportBoard', 'url' => route('boards.import'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!--} -->
                        <div class="board-image">
                                @if($board->type==='PINYA')
                                    @if($board->getSvgUrl($board->type))
                                        <img  src="{{ asset('media/colles/'.$board->colla->getShortname().'/svg/'.$board->getId().'_PINYA.svg') }}" width="100%"  >
                                    @elseif($board->type==='FOLRE')
                                        <img  src="{{ asset('media/colles/'.$board->colla->getShortname().'/svg/'.$board->getId().'FOLRE.svg') }}" width="100%"  >
                                    @elseif($board->type==='MANILLES')
                                        <img  src="{{ asset('media/colles/'.$board->colla->getShortname().'/svg/'.$board->getId().'MANILLES.svg') }}" width="100%"  >
                                    @elseif($board->type==='PUNTALS')
                                        <img  src="{{ asset('media/colles/'.$board->colla->getShortname().'/svg/'.$board->getId().'PUNTALS.svg') }}" width="100%"  >
                                    @else
                                        <img  src="{{ asset('media/img/ico_pinya_o3.svg') }}" width="80%"  >
                                    @endif
                                @endif
                        </div>
                            <div class="card-body pt-1">
                            <hr class="my-4">
                                <input name="colla_id_select"  type="hidden" value="{!! $board->colla->getId() !!}" >
                                <input name="pinya_id"  type="hidden" value="{!! $id_board !!}" >

                                <div class="row">
                                    <div class="ml-3 mt-2">
                                    <small class="text-muted">{!! trans('boards.template_rengles') !!}</small>

                                    @foreach ($posicions[$id_board]['rengla'] as $rengla)
                                        <li><input name="base_id"  type="hidden" value="{!! $rengla !!}" > {!! $rengla !!}
                                    @endforeach

                                </div>
                            </div>

                                <div class="row">
                                    <div class="ml-3 mt-2">
                                    <small class="text-muted">{!! trans('boards.pinya_positions') !!}</small>
                                        @foreach ($posicions[$id_board]['posicions'] as $posicio)
                                        <li>{!! $posicio !!}
                                        @endforeach
                                    </div>
                                </div>

                                <hr class="my-4">


                                <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <button class="btn btn-info btn-import-board" data-board="{{ $id_board }}"><i class="fa fa-clone"></i>{!! trans('boards.import_pinya') !!}</button>
                                </div>
                                <!-- {--!! Form::close() !!--} -->

                            </div>
                        </div>
                    </div>
                </div>
            <!-- END - Plantilla -->
            @endforeach
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

<!-- START - MODAL DELETE
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

        </div>
    </div><
</div> -->
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

        $('.btn-add-board').on('click', function (event)
        {
            $('#modalAddBoard').modal('show');
            console.log('set to public');
        });

        $('.btn-import-board').on('click', function (event)
        {
            $('#modalImportBoard').modal('show');
            $('#pinya_id')[0].value = event.currentTarget.attributes['data-board'].value;
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
