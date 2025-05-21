@extends('template.main')

@section('title', trans('boards.add_template'))
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
               <div class="col-md-3"><b>{!! trans('boards.add_template') !!}:</b></div>
               <div class="col-md-3">{!! trans('boards.import_step_1') !!}</div>
               <div class="col-md-3 text-success">{!! trans('boards.import_step_2') !!}</div>
               <div class="col-md-3 text-warning">{!! trans('boards.import_step_3') !!}</div>
           </div>
       </h3>
    </div>
    <div class="block-content">
        <div class="row form-group">
            <div class="col-md-12"><h2>{!! $board->name !!}</h2></div>
            <div class="col-md-12">
                <h5 class="text-info">{!! trans('boards.step_select_row_txt', ['BASE' => $type_map]) !!}</h5>
            </div>
            <div class="col-md-9">
                {!! trans('boards.step_select_row_explanation') !!}

            </div>
            <div class="col-md-3 text-right">
                <a class="btn btn-success" href="">{!! trans('boards.done_next_step') !!} <span class="fa fa-chevron-right"></span></a>
            </div>
        </div>

        <div class="row form-group" id="divName" style="visibility: hidden;">
            <div class="col-md-3">
                <label class="control-label">{!! trans('boards.row_name') !!}</label>
                <input type="text" name="name" id="name" class="form-control">
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
                <span class="text-success h5">Fet! Selecciona un altre baix</span>
            </div>
        </div>


        <div class="row">
            <div id="result_pinya" style="position: relative; height: 2000px;">
                @if($type_map==='PINYA')
                    {!! $board->html_pinya !!}
                @elseif($type_map==='FOLRE')
                    {!! $board->html_folra !!}
                @elseif($type_map==='MANILLES')
                    {!! $board->html_manilles !!}
                @elseif($type_map==='PUNTALS')
                    {!! $board->html_manilles !!}
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
        var id_row;
        $('#result_pinya').on('click', 'div', function ()
        {
            $('#divName').css('visibility', 'visible');
            id_row = $(this).attr('id');

            $(this).css('border', '4px solid grey');
            $(this).css('line-height', '23px');
        });

        $('#BtnRemoveRow').on('click', function ()
        {
            $('#'+id_row).css('border', '1px solid grey');
            id_row = null;
            $('#divName').css('visibility', 'hidden');
        });

        $('#BtnNameOk').on('click', function ()
        {
            $('#spinnerAddName').show();

            let name = $('#name').val();
            $('#'+id_row).html(name);
            console.log(name);

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
        var data = {!! $board->data !!};
        var type_map = '{!! strtolower($type_map); !!}';
        var structure = data[type_map].structure;

        $.each(structure, function(i, v)
        {
            console.log(v.baix);
            $('#'+v.baix).css('border', '4px solid grey');
            $('#'+v.baix).css('line-height', '23px');
            $('#'+v.baix).html(i);
        });
    }
</script>
@endsection
