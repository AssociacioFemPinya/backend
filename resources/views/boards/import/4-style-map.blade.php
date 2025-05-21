@extends('template.main')

@section('title', trans('boards.add'))
@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
@endsection
@section('css_after')
    <style>
        #resultPinya div {
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
        .border_9{ border: 2px double grey;}
        .border_10{ border: 3px double grey;}
        .border_11{ border: 4px double grey;}
        .border_12{ border: 5px double grey;}

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

        .shadow {box-shadow:  none; }
        .shadow_1 {box-shadow: 2px 2px 2px gray; }
        .shadow_2 {box-shadow:  4px 4px 4px gray; }
        .shadow_3 {box-shadow:  6px 6px 6px gray; }


    </style>
@endsection

@section('content')

<div class="block">
    <div class="block-header block-header-default">
       <h3 class="block-title">
           <div class="row">
               <div class="col-md-12"><b>{!! trans('boards.add') !!}:</b></div>
               <div class="col-md-3">{!! trans('boards.import_step_1') !!}</div>
               <div class="col-md-3">{!! trans('boards.import_step_2') !!}</div>
               <div class="col-md-3">{!! trans('boards.import_step_3') !!}</div>
               <div class="col-md-3 text-success">{!! trans('boards.import_step_4') !!}</div>
           </div>
       </h3>
    </div>
    <div class="block-content">
        <div class="row form-group">
            <div class="col-md-12"><h2>{!! $board->getName() !!}</h2></div>
            <div class="col-md-12">
                <h5 class="text-info">{!! trans('boards.step_style map_txt', ['BASE' => $type_map]) !!}</h5>
            </div>
            <div class="col-md-9">
                {!! trans('boards.step_style_map_explanation') !!}
            </div>
            <div class="col-md-3 text-right">
                <button class="btn btn-success" id="btnFinalImport">{!! trans('boards.done_next_step') !!} <span class="fa fa-chevron-right"></span></button>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-1">

               <label class="control-label">{!! trans('boards.borders') !!}</label>

                   <div class="row" style="padding-left: 15px;">
                        <div id="border_1" class="border_1 div-class" style="width: 25px; height: 25px; margin-top: 6px;"></div>
                        <div id="border_2" class="border_2 div-class" style="width: 25px; height: 25px; margin-top: 6px;"></div>
                        <div id="border_3" class="border_3 div-class" style="width: 25px; height: 25px; margin-top: 6px;"></div>
                        <div id="border_4" class="border_4 div-class" style="width: 25px; height: 25px; margin-top: 6px;"></div>
                    </div>
                    <div class="row" style="padding-left: 15px;">
                        <div id="border_5" class="border_5 div-class" style="width: 25px; height: 25px; margin-top: 6px;"></div>
                        <div id="border_6" class="border_6 div-class" style="width: 25px; height: 25px; margin-top: 6px;"></div>
                        <div id="border_7" class="border_7 div-class" style="width: 25px; height: 25px; margin-top: 6px;"></div>
                        <div id="border_8" class="border_8 div-class" style="width: 25px; height: 25px; margin-top: 6px;"></div>
                    </div>
                    <div class="row" style="padding-left: 15px;">
                        <div id="border_9" class="border_9 div-class" style="width: 25px; height: 25px; margin-top: 6px;"></div>
                        <div id="border_10" class="border_10 div-class" style="width: 25px; height: 25px; margin-top: 6px;"></div>
                        <div id="border_11" class="border_11 div-class" style="width: 25px; height: 25px; margin-top: 6px;"></div>
                        <div id="border_12" class="border_12 div-class" style="width: 25px; height: 25px; margin-top: 6px;"></div>
                    </div>

            </div>

            <div class="col-md-1">
                <div class="row" style="padding-left: 15px;">
                    <label class="control-label">{!! trans('boards.background') !!}</label>
                    <div id="bg_color" class="text-center  div-class" style="width: 10px; height: 25px; border: 1px solid white;"></div>
                    <div id="bg_color" class="text-center bg_color div-class" style="width: 35px; height: 25px; border: 1px solid grey;"></div>
                </div>
                <div class="row" style="padding-left: 15px;">
                    <div id="bg_color_1" class="text-center bg_color_1 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">1</div>
                    <div id="bg_color_2" class="text-center bg_color_2 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">2</div>
                    <div id="bg_color_3" class="text-center bg_color_3 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">3</div>
                </div>
                <div class="row" style="padding-left: 15px;">
                    <div id="bg_color_4" class="text-center bg_color_4 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">4</div>
                    <div id="bg_color_5" class="text-center bg_color_5 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">5</div>
                    <div id="bg_color_6" class="text-center bg_color_6 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">6</div>
                </div>
                <div class="row" style="padding-left: 15px;">
                    <div id="bg_color_7" class="text-center bg_color_7 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">7</div>
                    <div id="bg_color_8" class="text-center bg_color_8 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">8</div>
                    <div id="bg_color_9" class="text-center bg_color_9 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">9</div>
                </div>
                <div class="row" style="padding-left: 15px;">
                    <div id="bg_color_10" class="text-center bg_color_10 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">10</div>
                    <div id="bg_color_11" class="text-center bg_color_11 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">11</div>
                    <div id="bg_color_12" class="text-center bg_color_12 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">12</div>
                </div>
                <div class="row" style="padding-left: 15px;">
                    <div id="bg_color_13" class="text-center bg_color_13 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">10</div>
                    <div id="bg_color_14" class="text-center bg_color_14 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">11</div>
                    <div id="bg_color_15" class="text-center bg_color_15 div-class" style="width: 25px; height: 25px; border: 1px solid grey; margin-top: 6px;">12</div>
                </div>
            </div>

            <div class="col-md-1">
                <label class="control-label">{!! trans('boards.shadow') !!}</label>
                <div class="row" style="padding-left: 15px;">
                    <div id="shadow" class="shadow div-class" style="width: 65px; height: 25px; margin-top: 6px;">text 0</div>
                    <div id="shadow_1" class="shadow_1 div-class" style="width: 65px; height: 25px; margin-top: 6px;">text 1</div>
                </div>
                <div class="row" style="padding-left: 15px;">
                    <div id="shadow_2" class="shadow_2 div-class" style="width: 65px; height: 25px; margin-top: 6px;">text 2</div>
                    <div id="shadow_3" class="shadow_3 div-class" style="width: 65px; height: 25px; margin-top: 6px;">text 3</div>
                </div>
            </div>

            <div class="col-md-1" style="padding-left: 15px;">
                <label class="control-label">{!! trans('boards.corner') !!}</label>

                <div class="row"  style="padding-left: 15px;">
                    <div id="radius" class="radius div-class" style="width: 65px; height: 25px; border: 1px solid grey;"></div>
                    <div id="radius_1" class="radius_1 div-class" style="width: 65px; height: 25px; border: 1px solid grey; margin-top: 6px;"></div>
                    <div id="radius_2" class="radius_2 div-class" style="width: 65px; height: 25px; border: 1px solid grey; margin-top: 6px;"></div>
                    <div id="radius_3" class="radius_3 div-class" style="width: 65px; height: 25px; border: 1px solid grey; margin-top: 6px;"></div>
                    </div>
                </div>

            <div class="col-md-1" style="padding-top: 25px;">
                <button class="btn btn-success" id="BtnNameOk">{!! trans('general.add') !!}</button>
            </div>
            <div class="col-md-1" style="padding-top: 25px;">
                <button class="btn btn-danger" id="BtnRemoveRow">{!! trans('general.no') !!}</button>
            </div>
            <div class="col-md-1" style="padding-top: 27px;">
                <div class="spinner-border" role="status" id="spinnerAddClass" style="display: none;"><span class="sr-only">Loading...</span></div>
                <i class="fa-solid fa-check fa-2x text-success" id="divDone" style="display: none;"></i>
            </div>
        </div>

        <div class="row">
            <div id="resultPinya" style="position: relative; height: 2000px;">
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

<!-- START - MODAL FINAL STEP -->
<div class="modal fade" id="modalFinalStep" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document" id="modalFinalStepContent">

    </div><!-- /.modal-dialog -->
</div>
<!--/ END - MODAL FINAL STEP-->

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
        let id_row;
        let addClasses = {border: null, background: null, radius: null};

        $('#resultPinya').on('click', 'div', function () {

            id_row = null;
            id_row = $(this).attr('id');

            $.each( addClasses, function(key, value) {

                if(value !== null) {
                    $('#'+id_row).addClass(value);
                }
            });

        });


        $('.div-class').on('click', function () {

            if($(this).hasClass('border_1') ||$(this).hasClass('border_2') || $(this).hasClass('border_3') || $(this).hasClass('border_4')
            ||$(this).hasClass('border_5')  || $(this).hasClass('border_6') || $(this).hasClass('border_7') || $(this).hasClass('border_8')
            ||$(this).hasClass('border_9') || $(this).hasClass('border_10') || $(this).hasClass('border_11') || $(this).hasClass('border_12')
            ) {

                addClasses.border =  $(this).attr('id');
                $('#'+id_row).css('border', '');

                $('#'+id_row).removeClass('border_1');
                $('#'+id_row).removeClass('border_2');
                $('#'+id_row).removeClass('border_3');
                $('#'+id_row).removeClass('border_4');
                $('#'+id_row).removeClass('border_5');
                $('#'+id_row).removeClass('border_6');
                $('#'+id_row).removeClass('border_7');
                $('#'+id_row).removeClass('border_8');
                $('#'+id_row).removeClass('border_9');
                $('#'+id_row).removeClass('border_10');
                $('#'+id_row).removeClass('border_11');
                $('#'+id_row).removeClass('border_12');

            }
            if($(this).hasClass('shadow') || $(this).hasClass('shadow_1') || $(this).hasClass('shadow_2') || $(this).hasClass('shadow_3')) {
                if($(this).hasClass('shadow')) {

                    addClasses.shadow =  null;
                    } else {

                    addClasses.shadow =  $(this).attr('id');
                    }

                    $('#'+id_row).removeClass('shadow_1');
                    $('#'+id_row).removeClass('shadow_2');
                    $('#'+id_row).removeClass('shadow_3');
                }

            if($(this).hasClass('radius') || $(this).hasClass('radius_1') || $(this).hasClass('radius_2') || $(this).hasClass('radius_3')) {

                if($(this).hasClass('radius')) {

                    addClasses.radius =  null;
                } else {

                    addClasses.radius =  $(this).attr('id');
                }

                $('#'+id_row).removeClass('radius_1');
                $('#'+id_row).removeClass('radius_2');
                $('#'+id_row).removeClass('radius_3');
            }

            if($(this).hasClass('bg_color') || $(this).hasClass('bg_color_1') || $(this).hasClass('bg_color_2') || $(this).hasClass('bg_color_3')
            || $(this).hasClass('bg_color_4') || $(this).hasClass('bg_color_5') || $(this).hasClass('bg_color_6') || $(this).hasClass('bg_color_7')
            || $(this).hasClass('bg_color_8') || $(this).hasClass('bg_color_9') || $(this).hasClass('bg_color_10') || $(this).hasClass('bg_color_11')
            || $(this).hasClass('bg_color_12') || $(this).hasClass('bg_color_13') || $(this).hasClass('bg_color_14') || $(this).hasClass('bg_color_15') ) {

                if($(this).hasClass('bg_color')) {

                    addClasses.background =  null;

                } else {
                    addClasses.background =  $(this).attr('id');
                }

                $('#'+id_row).removeClass('bg_color_1');
                $('#'+id_row).removeClass('bg_color_2');
                $('#'+id_row).removeClass('bg_color_3');
                $('#'+id_row).removeClass('bg_color_4');
                $('#'+id_row).removeClass('bg_color_5');
                $('#'+id_row).removeClass('bg_color_6');
                $('#'+id_row).removeClass('bg_color_7');
                $('#'+id_row).removeClass('bg_color_8');
                $('#'+id_row).removeClass('bg_color_9');
                $('#'+id_row).removeClass('bg_color_10');
                $('#'+id_row).removeClass('bg_color_11');
                $('#'+id_row).removeClass('bg_color_12');
                $('#'+id_row).removeClass('bg_color_13');
                $('#'+id_row).removeClass('bg_color_14');
                $('#'+id_row).removeClass('bg_color_15');
            }

            $.each( addClasses, function(key, value) {

                if(value!=null) {
                    $('#'+id_row).addClass(value);
                }
            });
        });

        $('#BtnRemoveRow').on('click', function () {

            $('#'+id_row).addClass('border_1');

            $('#'+id_row).removeClass('bg_color_1');
            $('#'+id_row).removeClass('bg_color_2');
            $('#'+id_row).removeClass('bg_color_3');
            $('#'+id_row).removeClass('bg_color_4');
            $('#'+id_row).removeClass('bg_color_5');
            $('#'+id_row).removeClass('bg_color_6');
            $('#'+id_row).removeClass('bg_color_7');
            $('#'+id_row).removeClass('bg_color_8');
            $('#'+id_row).removeClass('bg_color_9');
            $('#'+id_row).removeClass('bg_color_10');
            $('#'+id_row).removeClass('bg_color_11');
            $('#'+id_row).removeClass('bg_color_12');
            $('#'+id_row).removeClass('bg_color_13');
            $('#'+id_row).removeClass('bg_color_14');
            $('#'+id_row).removeClass('bg_color_15');

            $('#'+id_row).removeClass('radius_1');
            $('#'+id_row).removeClass('radius_2');
            $('#'+id_row).removeClass('radius_3');

            $('#'+id_row).removeClass('border_1');
            $('#'+id_row).removeClass('border_2');
            $('#'+id_row).removeClass('border_3');
            $('#'+id_row).removeClass('border_4');
            $('#'+id_row).removeClass('border_5');
            $('#'+id_row).removeClass('border_6');
            $('#'+id_row).removeClass('border_7');
            $('#'+id_row).removeClass('border_8');
            $('#'+id_row).removeClass('border_9');
            $('#'+id_row).removeClass('border_10');
            $('#'+id_row).removeClass('border_11');
            $('#'+id_row).removeClass('border_12');


            id_row = null;
            addClasses = {border: null, background: null, radius: null};
        });

        $('#BtnNameOk').on('click', function ()
        {
            $('#spinnerAddClass').show();

            let html = $('#resultPinya').html();

            $.post( "{!! route('boards.style-map-ajax', ['board' => $board->getId(), 'map' => $type_map]) !!}",
                { html: html })
                .done(function(result) {
                    if(result) {

                        $('#spinnerAddClass').hide();
                        $('#divDoneAddName').show();
                        $('#row_name').val('');
                        setTimeout(function(){
                            $('#divDone').hide(200);
                        }, 2000)
                    }
                });
        });

        $('#btnFinalImport').on('click', function (e){
            e.preventDefault();

            $.get( "{{ route('boards.modal-finish-import', ['boardId' => $board->getId(), 'base' => $type_map]) }}")
                .done(function( data ) {
                    $('#modalFinalStepContent').html(data);
                    $('#modalFinalStep').modal('show');
                });
        });

        putPositions();
    });

    function putPositions()
    {
        let data = {!! json_encode($boardRows) !!};

        $.each(data, function(i, v) {

            let divId = v.div_id;
            if(v.position === 'baix') {

                $('#'+divId).html(v.row);
                $('#'+divId).css('border', '4px solid gray');
                $('#'+divId).css('line-height', '23px');
                $('#'+divId).css('color','black');
            } else {

                if(v.cord === 0) {

                    if(v.side === '') {

                        $('#'+divId).html(v.position);
                    } else {

                        let CL_side = v.side === 'LEFT' ? '{!! trans('general.CL_left') !!}' : '{!! trans('general.CL_right') !!}';
                        $('#'+divId).html(v.position+' '+CL_side);

                    }
                } else {

                    if(v.side === '') {

                        $('#'+divId).html(v.position+' '+v.cord);
                    } else {

                        let CL_side = v.side === 'LEFT' ? '{!! trans('general.CL_left') !!}' : '{!! trans('general.CL_right') !!}';
                        $('#'+divId).html(v.position+' '+v.cord+' '+CL_side);
                    }
                }

                $('#'+divId).css('line-height', '28px');
                $('#'+divId).addClass('border_1')
                $('#'+divId).attr('data-row', v.row);
                $('#'+divId).attr('data-position', v.position);
                $('#'+divId).attr('data-id_position', v.id_position);
                $('#'+divId).attr('data-cord', v.cord);
                $('#'+divId).attr('data-side', v.side);
            }
        });

    }
</script>
@endsection
