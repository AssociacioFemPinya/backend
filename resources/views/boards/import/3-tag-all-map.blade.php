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
            color: white;
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
               <div class="col-md-3">{!! trans('boards.import_step_2') !!}</div>
               <div class="col-md-3 text-success">{!! trans('boards.import_step_3') !!}</div>
               <div class="col-md-3 text-warning">{!! trans('boards.import_step_4') !!}</div>
           </div>
       </h3>
    </div>
    <div class="block-content">
        <div class="row form-group">
            <div class="col-md-12"><h2>{!! $board->getName() !!}</h2></div>
            <div class="col-md-12">
                <h5 class="text-info">{!! trans('boards.step_select_all_row_txt', ['BASE' => $type_map]) !!}</h5>
            </div>
            <div class="col-md-9">
                {!! trans('boards.step_select_all_row_explanation') !!}
            </div>
            <div class="col-md-3 text-right">
                <a class="btn btn-success" href="{!! route('boards.style-map', ['board' => $board->getId(), 'map' => $type_map]) !!}">{!! trans('boards.done_next_step') !!} <span class="fa fa-chevron-right"></span></a>
            </div>
        </div>

        <div class="row form-group" id="divInputs" style="visibility: hidden;">
            <div class="col-md-2">
                <label class="control-label">{!! trans('casteller.position') !!}</label>
                <select name="position" id="position" class="form-control" required>
                    @foreach($positions as $position)
                        <option value="{!! $position->getValue().'+=+'.$position->getId() !!}">{!! $position->getName() !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="control-label">{!! trans('casteller.side') !!}</label>
                <select name="side" id="side" class="form-control">
                    <option value="">{!! trans('general.no_side') !!}</option>
                    <option value="left">{!! trans('general.left') !!}</option>
                    <option value="right">{!! trans('general.right') !!}</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="control-label">{!! trans('casteller.cord') !!}</label>
                <input type="number" min="0" max="100" step="1" name="cord" id="cord" value="0" class="form-control">
            </div>
            <div class="col-md-1">
                <label class="control-label">{!! trans('boards.is_core') !!}</label>
                <input type="checkbox" name="core" id="core" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="control-label">{!! trans('boards.row_belongs') !!}</label>
                <select name="row" id="row" class="form-control" required>
                    @foreach($map_rows as $row)
                        <option value="{!! $row !!}">{!! $row !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1" style="padding-top: 25px;">
                <button class="btn btn-success" id="BtnNameOk">{!! trans('general.add') !!}</button>
            </div>
            <div class="col-md-1" style="padding-top: 25px;">

                <button class="btn btn-danger" id="BtnRemoveRow"><i class="fa fa-trash-o"></i></button>
            </div>
            <div class="col-md-1" style="padding-top: 27px;">
                <div class="spinner-border" role="status" id="spinnerAddName" style="display: none;"><span class="sr-only">Loading...</span></div>
            </div>
            <div class="col-md-1" id="divDone" style="padding-top: 30px; display: none;">
                <i class="fa-solid fa-check fa-2x text-success"></i>
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
                var token = $('meta[name="csrf-token"]').attr('content'); // or _token, whichever you are using

                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token); // adds directly to the XmlHttpRequest Object
                }
            });
        });
    </script>
<script>
    let row_colors = @json($row_color);

    $(function ()
    {
        let id_row;

        $('#result_pinya').on('click', 'div', function ()
        {
            if(id_row && id_row !== $(this).attr('id')) {

                $('#'+id_row).css('border', '1px solid grey');
                $('#divInputs').css('visibility', 'hidden');
                id_row = null;
            }

            if(id_row === $(this).attr('id')) {

                $('#divInputs').css('visibility', 'hidden');
                id_row = null;
                $(this).css('border', '1px solid grey');
            }
            else
            {
                $('#divInputs').css('visibility', 'visible');
                id_row = $(this).attr('id');

                $(this).css('border', '4px solid grey');
                $(this).css('line-height', '23px');
            }

        });

        $('#core').on('change', function ()
        {
            if($(this).prop('checked')) {

                $('#cord').prop( "disabled", true);
                $('#cord').val(0);
            } else {

                $('#cord').prop( "disabled", false);
            }
        });

        $('#BtnRemoveRow').click(function ()
        {
            let cord, side;
            let box = $('#'+id_row);
            let name = box.html();

            //row (rengla del castell)
            let row = box.data().row;

            //side
            if(typeof box.data().side != 'undefined') {

                side = box.data().side;
            } else {

                side = false;
            }

            //cord
            if(typeof box.data().cord != 'undefined') {

                cord = box.data().cord;
            } else {

                cord = 0;
            }

            if(name.search(' ') > 0) {

                name = name.substring(0, name.search(' '));
            }


            $.post( "{!! route('boards.delete-position', ['board' => $board, 'map' => $type_map]) !!}",
                {
                    name: name,
                    id_row: id_row,
                    row: row,
                    side: side,
                    cord: cord
                })
                .done(function( response ) {
                    if(response) {
                        box.css('border', '1px solid grey');
                        box.css('background-color', '');
                        box.html('');
                        id_row = null;
                        $('#divInputs').css('visibility', 'hidden');
                    }
                });
        });

        $('#BtnNameOk').on('click', function ()
        {
            $('#spinnerAddName').show();

            let position = $('#position').val();
            position = position.split('+=+');
            let id_pos = position[1]
            position = position[0]
            let cord = $('#cord').val();
            let core = $('#core').prop('checked');
            let row = $('#row').val();
            let side = $('#side').val();

            if(core) {

                if(side === "") {

                    $('#'+id_row).html(position);
                } else {

                    let cl_side = (side === 'left') ? "{!! trans('general.CL_left') !!}" : "{!! trans('general.CL_right') !!}";
                    $('#'+id_row).html(position+' '+cl_side);
                    //$('#'+id_row).addClass(row);
                    $('#'+id_row).attr('data-row', row);
                    //$('#'+id_row).addClass(side);
                    $('#'+id_row).attr('data-side', side);
                }

            } else {

                if(side === "") {

                    $('#'+id_row).html(position+' '+cord);
                } else {

                    let cl_side = (side === 'left') ? "{!! trans('general.CL_left') !!}" : "{!! trans('general.CL_right') !!}";
                    $('#'+id_row).html(position+' '+cord+' '+cl_side);
                    //$('#'+id_row).addClass(row);
                    $('#'+id_row).attr('data-row', row);
                    //$('#'+id_row).addClass(side);
                    $('#'+id_row).attr('data-side', side);
                }
            }

            $('#'+id_row).css('border', '1px solid #'+row_colors[row]);
            $('#'+id_row).css('background-color','#'+row_colors[row])
            $('#'+id_row).css('line-height', '28px');

            $('#'+id_row).addClass('cord_'+cord);
            $('#'+id_row).attr('data-cord', cord);
            //$('#'+id_row).addClass(position);
            $('#'+id_row).attr('data-position', position);
            $('#'+id_row).attr('data-id_position', id_pos);
            $('#'+id_row).attr('data-row', row);
            $('#'+id_row).attr('data-side', side);

            $.post( "{!! route('boards.tag-position', ['board' => $board, 'map' => $type_map]) !!}",
                {
                    rowId: id_row,
                    position: position,
                    id_position: id_pos,
                    cord: cord, core: core,
                    row: row,
                    side: side
                })
                .then(function(response) {

                    if(response) {
                        $('#spinnerAddName').hide();
                        $('#divDoneAddName').show();
                        $('#row_name').val('');
                        setTimeout(function(){
                            $('#divDone').hide(200);
                        }, 2000)
                    }
                }).fail(function(response, status){
                    console.log(response);
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
                $('#'+divId).css('border', '4px solid #'+row_colors[v.row]);
                $('#'+divId).css('line-height', '23px');
                $('#'+divId).css('color','black');
            } else {

                if(v.cord === 0) {

                    if(v.side === '') {

                        $('#'+divId).html(v.position);
                    } else {

                        let CL_side;
                        switch (v.side) {
                            case 'LEFT':
                                CL_side = '{!! trans('general.CL_left') !!}'
                                break;
                            case 'RIGHT':
                                CL_side = '{!! trans('general.CL_right') !!}'
                                break;
                            default:
                                CL_side = '';
                        }

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
                $('#'+divId).css('background-color','#'+row_colors[v.row])

                $('#'+divId).attr('data-row', v.row);
                $('#'+divId).attr('data-position', v.position);
                $('#'+divId).attr('data-cord', v.cord);
                $('#'+divId).attr('data-side', v.side);
            }
        });

    }
</script>
@endsection
