@extends('template.main')

@section('title', trans('boards.add'))
@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
@endsection
@section('css_after')
    <style>
        #result_pinya div {
            position: absolute;
            text-align: center;
            line-height: 28px;
            border: 1px solid grey;
            display: block;
            overflow: hidden;
            font-size: 11.5px;
            font-family: Helvetica, Verdana, sans-serif;
        }
    </style>
@endsection

@section('content')

<div class="block">
    <div class="block-header block-header-default">
       <h3 class="block-title">
           <div class="row">
               <div class="col-md-12"><b>{!! trans('boards.add') !!}:</b></div>
               <div class="col-md-3">{!! trans('boards.import_step_1') !!}</div>
               <div class="col-md-3 text-success">{!! trans('boards.import_step_2') !!}</div>
               <div class="col-md-3 text-warning">{!! trans('boards.import_step_3') !!}</div>
               <div class="col-md-3 text-warning">{!! trans('boards.import_step_4') !!}</div>
           </div>
       </h3>
    </div>
    <div class="block-content">
        <div class="row form-group">
            <div class="col-md-12"><h2>{!! $board->getName() !!}</h2></div>
            <div class="col-md-12">
                <h5 class="text-info">{!! trans('boards.step_select_row_txt', ['BASE' => $type_map]) !!}</h5>
            </div>
            <div class="col-md-9">
                {!! trans('boards.step_select_row_explanation') !!}

            </div>
            <div class="col-md-3 text-right">
                <a class="btn btn-success" href="{!! route('boards.tag-all-map', ['board' => $board->getId(), 'map' =>$type_map]) !!}">{!! trans('boards.done_next_step') !!} <span class="fa fa-chevron-right"></span></a>
            </div>
        </div>

        <div class="row form-group" id="divName" style="visibility: hidden;">
            <div class="col-md-3">
                <label class="control-label">{!! trans('boards.row_name') !!}</label>
                <input type="text" name="row_name" id="row_name" class="form-control">
            </div>
            <div class="col-md-1" style="padding-top: 25px;">
                <button class="btn btn-success" id="BtnNameOk">{!! trans('general.add') !!}</button>
            </div>
            <div class="col-md-1" style="padding-top: 25px;">

                <button class="btn btn-danger" id="BtnRemoveRow">{!! trans('general.no') !!}</button>
            </div>
            <div class="col-md-1" style="padding-top: 27px;">
                <div class="spinner-border" role="status" id="spinnerAddName" style="display: none;"><span class="sr-only">Loading...</span></div>
            </div>
            <div class="col-md-4" id="divDoneAddName" style="padding-top: 30px; display: none;">
                <span class="text-success h5">{!! trans('boards.select_other_row_baix') !!}</span>
            </div>
        </div>


        <div class="row">
            <div id="result_pinya" style="position: relative; height: 2000px;">
                @if($type_map === \App\Enums\BasesEnum::PINYA)
                    {!! $board->getHtmlPinya() !!}
                @elseif($type_map === \App\Enums\BasesEnum::FOLRE)
                    {!! $board->getHtmlFolre() !!}
                @elseif($type_map === \App\Enums\BasesEnum::MANILLES)
                    {!! $board->getHtmlManilles() !!}
                @elseif($type_map === \App\Enums\BasesEnum::PUNTALS)
                    {!! $board->getHtmlPuntals() !!}
                @endif
            </div>
        </div>

    </div>
</div>

@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <script type="text/javascript">
        $(function () {
            $.ajaxPrefilter(function(options, originalOptions, xhr) { // this will run before each request
                let token = $('meta[name="csrf-token"]').attr('content'); // or _token, whichever you are using

                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token); // adds directly to the XmlHttpRequest Object
                }
            });
        });
    </script>
<script>
    $(function ()
    {
        let id_row;
        $('#result_pinya').on('click', 'div', function ()
        {
            $('#divName').css('visibility', 'visible');
            id_row = $(this).attr('id');

            $(this).css('border', '4px solid grey');
            $(this).css('line-height', '23px');
        });

        $('#BtnRemoveRow').click(function (){

            let name = $('#'+id_row).html();

            $.post( "{!! route('boards.delete-position', ['board' => $board, 'map' => $type_map]) !!}", { name: name, id_row: id_row })
                .done(function( response ) {
                    if(response) {
                        $('#'+id_row).css('border', '1px solid grey');
                        $('#'+id_row).html('');
                        id_row = null;
                        $('#divName').css('visibility', 'hidden');
                    }
                });
        });


        $('#BtnNameOk').on('click', function ()
        {
            $('#spinnerAddName').show();

            let name = $('#row_name').val();
            $('#'+id_row).html(name);

            $.post( "{{ route('boards.tag-baix-position', ['board' => $board]) }}", { name: name, rowId: id_row, base: '{{$type_map}}' })
                .then(function(result) {

                    if(result) {
                        $('#spinnerAddName').hide();
                        $('#divDoneAddName').show();
                        $('#row_name').val('');
                        setTimeout(function(){
                            $('#divDoneAddName').hide(200);
                        }, 2500)

                    }
                }).fail(function(result){
                console.log(result);
            });
        });

        putNames();
    });

    function putNames()
    {
        let data = {!! json_encode($board->getData()) !!};
        let type_map = '{!! strtolower($type_map); !!}';

        if(data[type_map] !== null)
        {
            let structure = data[type_map].structure;

            $.each(structure, function(i, v) {

                $('#'+v.baix).css('border', '4px solid grey');
                $('#'+v.baix).css('line-height', '23px');
                $('#'+v.baix).html(i);
            });
        }
    }
</script>
@endsection
