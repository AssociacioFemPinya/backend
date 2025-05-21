@extends('template.public.main')

@section('title', 'FemPinya')
@section('css_after')
    <style>
        #pinya{
            position: relative;
            height: 1100px !important;
            /*width: 900px !important;*/
        }
        #pinya div{
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
            background-color: #fff;
        }

        .col-12{
            height:100% !important;
            max-height: 100% !important;
        }

        .element-selected{
            font-weight: bold !important;
            background-color: #f0f2f5 !important;
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

        .bg_color_1{background-color: #fadbd8 !important;}
        .bg_color_2{background-color: #f5b7b1 !important;}
        .bg_color_3{background-color: #f1948a !important;}
        .bg_color_4{background-color: #a9dfbf !important;}
        .bg_color_5{background-color: #52be80 !important;}
        .bg_color_6{background-color: #27ae60 !important;}
        .bg_color_7{background-color: #f9e79f !important;}
        .bg_color_8{background-color: #f4d03f !important;}
        .bg_color_9{background-color: #FFD700 !important;}
        .bg_color_10{background-color: #f5cba7 !important;}
        .bg_color_11{background-color: #f0b27a !important;}
        .bg_color_12{background-color: #eb984e !important;}
        .bg_color_13{background-color: #81d4fa !important;}
        .bg_color_14{background-color: #29b6f6 !important;}
        .bg_color_15{background-color: #039be5 !important;}

        .shadow_0 {box-shadow:  none; }
        .shadow_1 {box-shadow: 2px 2px 2px gray; }
        .shadow_2 {box-shadow:  4px 4px 4px gray; }
        .shadow_3 {box-shadow:  6px 6px 6px gray; }

        .btn-casteller {
            color: #212529;
            background-color: #fff;
            border-color: #cbd2dd;
            font-size: 13px;
            font-weight: normal;
            width: 100%;
            font-family: 'Microsoft Sans Serif', Tahoma, Arial, Verdana, Sans-Serif, serif;
            white-space: nowrap;
            overflow: hidden;
            padding-top: 12px;
        }

        .positioned{
            color: #575757;
            background-color: #fff;
            border-color: #575757;
            font-size: 13px;
            font-weight: normal;
            width: 100%;
            font-family: 'Microsoft Sans Serif', Tahoma, Arial, Verdana, Sans-Serif, serif;
            white-space: nowrap;
            overflow: hidden;
            padding-top: 12px;
        }

        .span-name{
            vertical-align: top;
            color: #3f9ce8;
            font-weight: 200;
        }

        .span-name-positioned{
            vertical-align: top;
            color: #7a8998;
            font-weight: 200;
        }

        .attenuated {
            opacity: 0.7;
        }

        .highlighted {
            opacity: 1;
            font-weight: 600;
            box-shadow: 0 0 0 4px #ff0000;
        }

        .projector {
            font-weight: 800;
        }



    </style>

@endsection

@section('content')

    <div class="row no-gutters justify-content-center">
        <div class="col-12">
            <div id="pinya" class="ml-10 mt-5">{!! $base !!}</div>
        </div>
    </div>

@endsection

@section('js')

    <script src='https://unpkg.com/panzoom@9.4.0/dist/panzoom.min.js'></script>

    <script type="text/javascript">
        $(function () {
            $.ajaxPrefilter(function(options, originalOptions, xhr) { // this will run before each request
                const token = $('meta[name="csrf-token"]').attr('content'); // or _token, whichever you are using

                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token); // adds directly to the XmlHttpRequest Object
                }
            });
        });
    </script>
    <script>
        let base = "{!! $castellerBase !!}";
        let positions = {!! json_encode($positions) !!};
        let castellerId = {{ $castellerId }};
        let castellerDivId = '';
        let boardEventId = parseInt({{ $boardEvent->getId() }});
        let pinyazoom = null;

        function loadMap(base){

            let url = "{{ route('public.display.load-map', ['token' => $token, 'shortName' => $shortName, 'base' => ':base']) }}";
            url = url.replace(':base', base);

            $.get(url)
                .then(function(result, status){
                    loadCastellers(result);
                }).fail(function(result, status){

            });
        }

        function loadCastellers(result){
            result.forEach(function(e, i){
                $('#' + e.row.div_id).html(e.casteller.alias);
                if (castellerId == e.casteller.id_casteller) {
                    $('#' + e.row.div_id).addClass("highlighted");
                    castellerDivId = e.row.div_id;
                }
            });
        }

        function setOrientation(){
            let orientation = (screen.orientation || {}).type || screen.mozOrientation || screen.msOrientation;
            let pinyaDiv = document.getElementById('pinya');
            let mainContainer = $("#main-container");

            switch (orientation) {
                case "landscape-primary":
                   /* pinyaDiv.style.minHeight = $(window).height()+"px";
                    pinyaDiv.style.height = Screen.height+"px";*/
                    pinyaDiv.style.width = $(window).width()+"px";
                    console.log("Landscape");
                    break;
                case "portrait-primary":
                   /* pinyaDiv.style.minHeight = $(window).height()+"px";
                    pinyaDiv.style.height = Screen.height+"px";*/
                    pinyaDiv.style.width = $(window).width()+"px";
                    console.log("Portrait");
                    break;
                default:
                    console.log("The orientation API isn't supported in this browser :(");
            }
        }

        function changeBase(newBase){

            let url = "{{ route('event.board.load-base', ['boardEvent' => $boardEvent->getId(), 'base' => ':base']) }}";
            url = url.replace(':base', newBase);

            let pinya = $('#pinya');
            pinya.hide(400, function(){
                $.get(url).then(function(result, status){
                    base = newBase;
                    pinya.html(result);
                    $('#pinya div').html('');
                    loadMap(base);
                    pinya.show(400);
                });
            });
        }


    $(function ()
    {
        /*getPinyaHeight();*/

       $( window ).on( "orientationchange", function( event ) {
            setOrientation();
        });

       //setOrientation();

        $('#pinya div').html('');

        $('#base').change(function(){
            let newBase = $(this).val();
            changeBase(newBase);
        });

        if (castellerId != 0) {
            $('#pinya div').addClass('attenuated');
        } else {
            $('#pinya div').addClass('projector');
        }

        loadCastellers(positions);

        $('#base').val(base);

        let pinyaDiv = document.getElementById('pinya');

        /* Detect if the device has "touch Screen" property  */
        if ("ontouchstart" in document.documentElement) {
            pinyazoom = panzoom(pinyaDiv, {
                maxZoom: 1.8,
                minZoom: 0.5,
                initialZoom: 0.75
            });

            if(castellerDivId != ''){
                measures = document.getElementById(castellerDivId).getBoundingClientRect();
                pinyazoom.moveTo(0, 0);
                pinyazoom.smoothMoveTo(-measures.x/2, -measures.y/2);

                $('#fixedbutton').on( "click", function( event ) {
                    pinyazoom.pause();
                    pinyazoom.resume();
                    pinyazoom.zoomAbs(0, 0, 1);
                    pinyazoom.moveTo(0, 0);
                    pinyazoom.smoothMoveTo(-measures.x/2, -measures.y/2);
                });
            }

        }

        });
    </script>

    <script type="text/javascript">let colla_shortname = '{{ $shortName }}';</script>
    <script src="{{ asset('js/pages/listen_display_channel.js') }}"></script>
@endsection
