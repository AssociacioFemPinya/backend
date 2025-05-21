@extends('template.main-boxed')

@section('title', trans('general.board'))
@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
@endsection
@section('css_after')
    <style>

        #page-container.page-header-fixed #page-header {
            position: inherit;
        }
        #page-container.page-header-fixed #main-container {
            padding-top: 0px;
        }

        #page-container.main-content-boxed > #page-header .content-header,
        #page-container.main-content-boxed > #page-header .content,
        #page-container.main-content-boxed > #main-container .content,
        #page-container.main-content-boxed > #page-footer .content {
            max-width: none;
        }

        #pinya div {
            position: absolute;
            text-align: center;
            line-height: 28px;
            display: block;
            overflow: hidden;
            font-size: 10.5px;
            width: 72px;
            height: 31.6px;
            font-family: Helvetica, Verdana, sans-serif;
            color: black;
            background-color: #fff;
            cursor: pointer;
        }

        div#board {
            overflow-x: scroll;
            height: 100vh;
        }

        @media (max-width: 800px) {
            div#board {
                width: 100%;
                max-width: 100%;
                flex: 0 0 100%;
            }
        }


        header div.content-header {
            height: auto;
            margin-right: 0;
            margin-left: 0;
            max-width: none;
            justify-content: center;
        }

        @media (min-width: 992px) {
            header .content-header-section.section-title-container {
                flex: 0 0 40%;
                text-align: center;
            }
            header div.content-header {
                justify-content: start;
                margin-right: 160px;
                margin-left: 195px;
            }
        }

        @media (max-width: 992px) {
            header .content-header-section.section-title-container {
                display:none;
            }
            .content-header-section span.profile-text {
                display: none;
            }
            header div.content-header {
                margin-right: 90px;
                margin-left: 195px;
            }
        }

        @media (max-width:576px){
            .logo-container{
                display: none;
            }
            header div.content-header {
                margin-right: 90px;
                margin-left: 70px;
            }
        }

        header .user-btn {
            position: absolute;
            right: 10px;
        }

        header .content-header-section.section-logo-container  {
            position: absolute;
            left: 10px;
        }


        #listBoardsGroup{
            position: unset;
        }

        #listboard div {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 0 7px 7px;

        }

        #listboard div:first-child {
            padding-top: 15px;
        }

        #listboard div a {
            padding-right: 15px;
            line-height: 16px;
            border-radius: 3px;
            color: #575757;

        }
        #listboard div a:hover{
            color: #000;
        }

        #listboard.hover{
            display: block;
            position: absolute;
            will-change: transform;
            transform: translate3d(0px, 34px, 0px);
            z-index: 1032;
        }

        #pinya div.element-selected{
            font-weight: bold !important;
            background-color: rgba(87, 87, 87, .3) !important;
            box-shadow: 0 0 0 3px rgb(52, 58, 64, .7);
            /*border: solid 5px rgb(52, 58, 64, .7);*/
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

        .highlighted {
            opacity: 1;
            font-weight: 600;
            box-shadow: 0 0 0 3px #ff0000;
        }

        .highlighted-not-active {
            border: dashed;
            font-weight: 600;
            color: #ff0000 !important;
            border-color: #ff0000;
        }

        .span-name{
            vertical-align: top;
            color: #3f9ce8;
            font-weight: 200;
        }

        .span-name-positioned{
            vertical-align: top;
            color: #7a8998 !important;
            font-weight: 200;
        }

        .btn-casteller:hover, .btn-casteller:focus, .btn-casteller.focus {
            color: #212529;
            background-color: #f0f2f5;
            border-color: #adb8c8;
        }

        .btn-casteller.disabled,
        .btn-casteller:disabled {
            background-color:#f0f2f5;
            border-color:#cbd2dd
        }

        .pointer{
            cursor: pointer;
        }

        .disabled{
            opacity: 0.70;
        }

        .content-header {
            flex-wrap: wrap;
        }

        .vertical-line{
            font-weight: bold !important;
        }

        button:disabled{
            pointer-events: unset !important;
            cursor: unset;
        }

        .tooltip-inner{
            background-color: rgb(108, 117, 125);
        }

        #sub-header{
            padding: 10px 20px;
        }

        .icons{
            align-items: center;
        }

        #blockPositionsContent{
            /*flex-wrap: wrap;*/
        }

        #searchByNameText{
            padding: 0.5rem !important;
            min-width: 130px;
        }

        #resetFilter{
            cursor:pointer;
        }

    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="block mb-0">
                <div id="sub-header" class="block-content">
                    <div class="row pb-10 ">
                        <div id="positions-list" class="col-12 col-lg-6 pt-5">
                            <label class="col-12 ml-0 pl-0 text-gray-darker ">{!! trans('casteller.positions') !!}</label>
                            @foreach ($positions as $position)
                                <button class="btn btn-outline-primary btn-load-position" data-id_position="{{ $position->getId() }}">{!! $position->getName() !!}</button>
                            @endforeach
                            <button class="btn btn-outline-primary btn-load-position" data-id_position="0">{!! trans('event.all') !!}</button>
                        </div>
                        <div class="col-6 col-sm-3 col-lg-1 pr-5 pl-5 text-center pt-5">
                            <label class="ml-0 pl-0 text-gray-darker"><i class="fa-solid fa-check"></i></label>
                            <select name="status[]" id="status" class="attendance selectize2 form-control" multiple>
                                <option value="{{ \App\Enums\AttendanceStatus::YES }}">{!! trans('attendance.' . \App\Enums\AttendanceStatus::YES) !!}</option>
                                <option value="{{ \App\Enums\AttendanceStatus::NO }}">{!! trans('attendance.' . \App\Enums\AttendanceStatus::NO) !!}</option>
                                <option value="{{ \App\Enums\AttendanceStatus::UNKNOWN }}">¿?</option>
                            </select>
                        </div>
                        <div class="col-6 col-sm-3 col-lg-1 pr-5 pl-5 text-center pt-5">
                            <label class="ml-0 pl-0 text-gray-darker"><i class="fa-solid fa-check-double"></i></label>
                            <select name="statusVerified[]" id="statusVerified" class="attendance selectize2 form-control" multiple>
                                <option value="{{ \App\Enums\AttendanceStatus::YES }}">{!! trans('attendance.' . \App\Enums\AttendanceStatus::YES) !!}</option>
                                <option value="{{ \App\Enums\AttendanceStatus::NO }}">{!! trans('attendance.' . \App\Enums\AttendanceStatus::NO) !!}</option>
                                <option value="{{ \App\Enums\AttendanceStatus::UNKNOWN }}">¿?</option>
                            </select>
                        </div>
                        <div class="col-6 col-sm-3 col-lg-1 pr-5 pl-5 text-center pt-5">
                            <label class="ml-0 pl-0 text-gray-darker">{!! trans('casteller.height_type') !!}</label>
                            <select name="height_type[]" id="height_type" class="heights selectize2 form-control">
                                <option value="height" selected>{!! trans('casteller.height') !!}</option>
                                <option value="shoulderHeight">{!! trans('casteller.shoulder_height') !!}</option>
                            </select>
                        </div>
                        <div class="col-6 col-sm-3 col-lg-1 pr-5 pl-5 text-center pt-5">
                            <label class="ml-0 pl-0 text-gray-darker">{!! trans('general.options') !!}</label>
                            <div>
                                <i id="resetFilter" class="fa-solid fa-2x fa-arrow-rotate-right text-primary" data-toggle="tooltip" data-placement="right" title="{!! trans('general.tooltip_reset_filter') !!}"></i>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-lg-1 pr-5 pl-5 text-center pt-5">
                            @if($event->getBoards()->isNotEmpty() && $board->hasFolre())
                                <label class="ml-0 pl-0 text-gray-darker ">{!! trans('boards.select_base') !!}</label>
                                <select name="base" id="base" class="form-control">
                                    <option value="{{ \App\Enums\BasesEnum::PINYA }}">{{ \App\Enums\BasesEnum::PINYA }}</option>
                                    <option value="{{ \App\Enums\BasesEnum::FOLRE }}">{{ \App\Enums\BasesEnum::FOLRE }}</option>
                                    @if($board->hasManilles())<option value="{{ \App\Enums\BasesEnum::MANILLES }}">{{ \App\Enums\BasesEnum::MANILLES }}</option>@endif
                                    @if($board->hasPuntals())<option value="{{ \App\Enums\BasesEnum::PUNTALS }}">{{ \App\Enums\BasesEnum::PUNTALS }}</option>@endif
                                </select>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





    <div class="row pr-2 pl-2">

        <div class="col-sm-12 col-md-3 pr-10 pt-10 positions-block" id="blockPositions">
            <div class="mb-10 text-right">
                <div class="row d-block">
                    <div id="blockPositionsContent" class="d-flex " style="margin: 0 15px">
                        <div class="d-flex pt-5 pr-5">
                            <div class="input-group-prepend"  data-toggle="tooltip" data-placement="right" title="{!! trans('boards.tooltip_search') !!}">
                                <button class="btn btn-secondary" disabled><i class="fa fa-search"></i></button>
                            </div>
                            <input type="text" id="searchByNameText" placeholder="{!! trans('general.text_to_search') !!}" class="form-control">
                        </div>
                        <div class="d-flex icons pt-5">
                            <i id="emptyBoard" class="fa-regular fa-user fa-2x btn-empty-board pr-5 pointer" data-toggle="tooltip" data-placement="right" title="{!! trans('boards.tooltip_empty_board') !!}"></i>
                            <i id="removeMissingCastellers" class="fa-solid fa-user-slash fa-2x btn-remove-missing pr-5 pointer" data-toggle="tooltip" data-placement="right" title="{!! trans('boards.tooltip_remove_missing') !!}"></i>
                            <i class="fa-solid fa-user-xmark fa-2x pr-5 btn-trash text-muted" data-toggle="tooltip" data-placement="right" title="{!! trans('boards.tooltip_empty_row') !!}"></i>
                        </div>
                    </div>
                </div>


            </div>
            <div class="block block-bordered mb-2">
                <div class="block-header block-header-default">
                    <span class="font-w600 h5 m-0" id="positionName"></span>
                </div>
                <div class="block-content">
                    <div class="row" id="positionRows"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-9" id="board">
            <div id="pinya" class="ml-10 mt-5">
                {!!  $board?->getHtmlPinya() !!}
            </div>

        </div>
    </div>
    <!-- START - Modal short -->
    <div class="modal fade" id="modalEmptyBoard" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-popin" role="document">
            <div class="modal-content" id="modalEmptyBoardContent">
                <!-- MODAL CONTENT -->
                @include('events.boards.modals.modal-empty-board')
            </div>
        </div>
    </div>
    <!-- END - Modal short -->
    <!-- START - Modal long -->
    <div class="modal fade" id="modalRemoveMissingCastellers" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popin" role="document">
            <div class="modal-content" id="modalRemoveMissingCastellersContent">
                <!-- MODAL CONTENT -->
                @include('events.boards.modals.modal-remove-missing-castellers')
            </div>
        </div>
    </div>
    <!-- END - Modal long -->
    <!-- START - Modal long -->
    <div class="modal fade" id="modalAttachBoardOnEvent" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popin" role="document">
            <div class="modal-content" id="modalAttachBoardOnEventEventContent">
                <!-- MODAL CONTENT -->
                @include('events.boards.modals.modal-attach')
            </div>
        </div>
    </div>
    <!-- END - Modal long -->
    <!-- START - Modal long -->
    <div class="modal fade" id="modalImportBoardOnEvent" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popin" role="document">
            <div class="modal-content" id="modalImportBoardOnEventEventContent">
                <!-- MODAL CONTENT -->
                @include('events.boards.modals.modal-import')
            </div>
        </div>
    </div>
    <!-- END - Modal long -->
    <!-- START - Modal long -->
    <div class="modal fade" id="modalEditBoardEvent" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popin" role="document">
            <div class="modal-content" id="modalEditBoardEventEventContent">
                <!-- MODAL CONTENT -->
                @include('events.boards.modals.modal-edit')
            </div>
        </div>
    </div>
    <!-- END - Modal long -->
    <!-- The Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-popin"  role="document">
        <div class="modal-content">
            {!! Form::open(array('url' => '', 'method' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'formDelBoardEvent')) !!}
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>

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

        var positionId = 0;
        let firstPositionId = 0;
        let castellerId;
        let idCasteller;
        let rowId;
        let boardEventId = parseInt({{ $boardEvent->getId() }});
        let base = 'PINYA';
        let btnTrash = $('.btn-trash');
        let firstPointerType = null;

        jQuery(window).one('pointermove', function (e) {
            firstPointerType = e.originalEvent.pointerType;
        });

        function initFilters(){

            if (localStorage.getItem('board_event_height_type')) {
                $('#height_type').val(localStorage.getItem('board_event_height_type')).trigger('change');
            }
            if (localStorage.getItem('board_event_status')) {
                var status = JSON.parse(localStorage.getItem('board_event_status'));
                $('#status').val(status).trigger('change');
            }
            if (localStorage.getItem('board_event_status_verified')) {
                var statusVerified = JSON.parse(localStorage.getItem('board_event_status_verified'));
                $('#statusVerified').val(statusVerified).trigger('change');
            }
            if (localStorage.getItem('board_event_position')) {
                positionId = localStorage.getItem('board_event_position');
            }
            let positionLoaded = $('#positions-list button[data-id_position="' + positionId + '"]');
            positionLoaded.addClass('btn-primary');
            positionLoaded.removeClass('btn-outline-primary');
        }

        function resetFilters(){
                localStorage.removeItem('board_event_height_type');
                localStorage.removeItem('board_event_status');
                localStorage.removeItem('board_event_status_verified');
                localStorage.removeItem('board_event_position');
                positionId = firstPositionId;
                $('#height_type').val('height').trigger('change');
                $('#status').val(null).trigger('change');
                $('#statusVerified').val(null).trigger('change');
                $('.btn-load-position[data-id_position="' + positionId + '"]').click();
            }


        function loadCastellersList(positionId) {

            let heightType = $('#height_type').val();
            let attendanceStatus = $('#status').val();
            let attendanceStatusVerified = $('#statusVerified').val();
            let filterText = $('#searchByNameText').val();

            $.post(
                "{{ route('event.board.load-positions-ajax', ['boardEvent' => $boardEvent]) }}",
                {
                    positionId: positionId,
                    attendanceStatus: attendanceStatus,
                    attendanceStatusVerified: attendanceStatusVerified,
                    filterText: filterText,
                    heightType: heightType
                }
            ).then(function (result, status){
                $('#positionRows').html(result.rows);
                $('#positionName').html(result.position.name);
                $('.tooltip').remove();
                $('#positionRows [data-toggle="tooltip"]').tooltip();
            }).fail(function (result, status) {

            });
        }

        $('#searchByNameText').on('input', function() {
            loadCastellersList(null);
        });


        $('#resetFilter').on('click', function (event)
        {
            resetFilters();
        });

        function putCastellerOnPinya(idCasteller, idRow, boardEventId){

            let url = "{{ route('event.board.put-casteller-ajax', ['eventBoardId' => ':eventBoardId']) }}";
            url = url.replace(':eventBoardId', boardEventId);

            $.post(url, {castellerId: idCasteller, rowId: idRow, eventId: {{ $event->getId() }}, base: base})
                .then(function(result, status){
                    castellerDiv(result.divId, result.castellerName, result.castellerHeight, result.castellerAttendance, result.castellerVerifiedAttendance, result.castellerShoulderHeight, result.castellerActivePinya);
                    loadCastellersList(positionId);
                }).fail(function(result, status){
                }
            );

            resetVars();
        }

        function emptyRow(idRow, boardEventId){

            let url = "{{ route('event-board.empty-row-pinya', ['boardEvent' => ':boardEvent']) }}";
            url = url.replace(':boardEvent', boardEventId);

            $.post(url, {divId: idRow, eventId: {{ $event->getId() }}, base: base})
                .then(function(result, status){
                    $('#' + idRow).html('');
                    loadCastellersList(positionId);
                }).fail(function(result, status){
                }
            );

            resetVars();
        }

        function castellerDiv(divId, castellerName = "", castellerHeight, castellerAttendance, castellerVerifiedAttendance, castellerShoulderHeight, activePinya){
            let colorAttendance = '{!! \App\Helpers\RenderHelper::getAttendanceIconEditor(\App\Enums\ScaledAttendanceStatus::UNKNOWN) !!}';
            let height_type = $('#height_type').val();
            let height = castellerHeight;
            if(height_type === 'shoulderHeight') height = castellerShoulderHeight;

            $('#' + divId).html('');
            $('#' + divId).removeClass("highlighted-not-active");

            if(castellerName != ''){

                if(castellerVerifiedAttendance == 1) {
                    colorAttendance = '{!! \App\Helpers\RenderHelper::getAttendanceIconEditor(\App\Enums\ScaledAttendanceStatus::YESVERIFIED) !!}';
                }
                else if(castellerVerifiedAttendance == 2) {
                    colorAttendance = '{!! \App\Helpers\RenderHelper::getAttendanceIconEditor(\App\Enums\ScaledAttendanceStatus::NO) !!}';
                }
                else if(castellerAttendance == 1) {
                    colorAttendance = '{!! \App\Helpers\RenderHelper::getAttendanceIconEditor(\App\Enums\ScaledAttendanceStatus::YES) !!}';
                }
                else if(castellerAttendance == 2) {
                    colorAttendance = '{!! \App\Helpers\RenderHelper::getAttendanceIconEditor(\App\Enums\ScaledAttendanceStatus::NO) !!}';
                }

                iAttendance = '<i class="fa ' + colorAttendance + ' mt-5 pt-1"></i>';

            if(activePinya == false) $('#' + divId).addClass("highlighted-not-active");

                $('#' + divId).html(
                    '<span style="font-size: 10px; width:100%; float:left; position: absolute; top: -8px; left: 0px; ">'  + iAttendance +' '+ height+ '</span>'
                    + '<span style="width:100%; position: absolute; top: 2px; left: 0px; ">' + castellerName + '</span>'
                );
            }
        }

        function swapCastellersOnPinya(boardEventId, idRow, idRowSwap){

            let url = "{{ route('event.board.swap-castellers', ['eventBoardId' => ':eventBoardId']) }}";
            url = url.replace(':eventBoardId', boardEventId);

            $.post(url, {rowId: idRow, rowSwapId: idRowSwap, eventId: {{ $event->getId() }}, base: base})
                .then(function(result, status){
                    castellerDiv(result.divId, result.castellerName, result.castellerHeight, result.castellerAttendance, result.castellerVerifiedAttendance, result.castellerShoulderHeight, result.castellerActivePinya);
                    castellerDiv(result.divSwappedId, result.castellerSwappedName, result.castellerSwappedHeight, result.castellerSwappedAttendance, result.castellerSwappedVerifiedAttendance, result.castellerSwappedShoulderHeight, result.castellerSwappedActivePinya);
                }).fail(function(result, status){

                }
            );

            resetVars();
        }

        function loadMap(boardEventId, base){

            let url = "{{ route('event.board.load-map-ajax', ['boardEvent' => ':boardEvent', 'base' => ':base']) }}";
            url = url.replace(':boardEvent', boardEventId);
            url = url.replace(':base', base);

            $.get(url).then(function(result, status){

                result.forEach(function(e, i){
                    castellerDiv(e.row.div_id, e.casteller.alias, e.casteller.height, e.casteller.castellerAttendance, e.casteller.castellerVerifiedAttendance, e.casteller.shoulderHeight, e.casteller.activePinya);
                });


            }).fail(function(result, status){});
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
                    loadMap(boardEventId, base);
                    resetVars();

                    pinya.show(400);
                });
            });
        }

        function resetVars(){

            if(typeof idCasteller !== 'undefined'){
                idCasteller = null;
            }
            castellerId = null;
            rowId = null;
            rowIdSwap = null;
            disableTrash();
        }

        function enableTrash(){

            btnTrash.removeClass('text-muted');
            btnTrash.addClass('text-danger');
            btnTrash.addClass('pointer');

        }

        function disableTrash(){

            btnTrash.addClass('text-muted');
            btnTrash.removeClass('text-danger');
            btnTrash.removeClass('pointer');

        }

        $(function ()
        {
            $('#pinya div').html('');

            $('#base').change(function(){

                let newBase = $(this).val();
                changeBase(newBase);
            });

            initFilters();
            loadCastellersList(positionId);
            loadMap(boardEventId, base);

            @if($event->getBoards()->isEmpty())
                $('#modalAttachBoardOnEvent').modal('show');
            @endif

            $('.selectize2').select2({language: "{{ Auth::user()->getLanguage() }}"});

            $('#btnToggleSidebarLeft').on('click', function(e) {

                let board = $('#board');

                if (board.hasClass('col-9')) {
                    board.addClass('col-12');
                    board.removeClass('col-9');
                    board.removeClass('table-responsive');
                    board.addClass('ml-20');
                    $('#blockPositions').hide(200);
                } else {
                    board.removeClass('ml-20');
                    board.removeClass('col-12');
                    board.addClass('col-9');
                    board.addClass('table-responsive');
                    $('#blockPositions').show(200);
                    board.height(1200);
                }
            });

            $('#positions-list button').on('click', function(e){
                $('#searchByNameText')[0].value = '';

                let allPositionsBtn = $('.btn-load-position');
                let positionClicked = $(this);
                positionId = parseInt(positionClicked.data('id_position'));
                localStorage.setItem('board_event_position', positionId);

                allPositionsBtn.removeClass('btn-primary');
                allPositionsBtn.addClass('btn-outline-primary');
                positionClicked.addClass('btn-primary');
                positionClicked.removeClass('btn-outline-primary');

                loadCastellersList(positionId);
            });

            $('#status,#statusVerified').on('change', function(e){
                var seletedStatus = $('#status').val();
                localStorage.setItem('board_event_status', JSON.stringify(seletedStatus));
                var seletedStatusVerified = $('#statusVerified').val();
                localStorage.setItem('board_event_status_verified', JSON.stringify(seletedStatusVerified));
                loadCastellersList(positionId);
            });

            $('#height_type').on('change', function(e){
                localStorage.setItem('board_event_height_type', $(this).val());
                loadMap(boardEventId, base);
                loadCastellersList(positionId);
            });

            $('#positionRows').on('click', '.positioned', function(){

                $('.highlighted').removeClass('highlighted');
                let el = $(this);
                let rowIdpos = el.data().id_row;

                if(rowIdpos && rowIdpos != ''){
                    $("#" + rowIdpos).addClass('highlighted');

                }

            });

            $('#positionRows').on('click', '.btn-casteller', function(){
                $('.element-selected').removeClass('element-selected');
                $('.highlighted').removeClass('highlighted');
                let el = $(this);

                el.addClass('element-selected');
                castellerId = el.data().id_casteller;

                // put casteller on pinya if an empty div is selected
                if(rowId && $('#' + rowId).html() === ''){
                    putCastellerOnPinya(castellerId, rowId, boardEventId);
                    resetVars();
                }
            });

            $('#pinya').on('click', 'div', function (){

                let el = $(this);
                $('.element-selected').removeClass('element-selected');
                $('.highlighted').removeClass('highlighted');
                el.addClass('element-selected');

                if(castellerId){
                    rowId = el.attr('id');
                    putCastellerOnPinya(castellerId, rowId, boardEventId);
                    el.removeClass('element-selected');
                    resetVars();
                } else {

                    positionId = el.data("id_position");

                    if(rowId){

                        let rowIdSwap = el.attr('id');

                        if($('#' + rowId).html() !== '' && rowId != rowIdSwap){
                            swapCastellersOnPinya(boardEventId, rowId, rowIdSwap)
                            el.removeClass('element-selected');
                            resetVars();
                        }else if(el.html() !=='' && rowId != rowIdSwap){
                            swapCastellersOnPinya(boardEventId, rowIdSwap, rowId)
                            el.removeClass('element-selected');
                            resetVars();
                        }else if(rowId == rowIdSwap){
                            el.removeClass('element-selected');
                            resetVars();
                        }else{
                            rowId = el.attr('id');
                            rowIdSwap = null;
                            $('.btn-load-position[data-id_position="' + positionId + '"]').click();

                        }

                    } else {


                        if(el.html() !== ''){
                            enableTrash();
                        }

                        rowId = el.attr('id');
                        $('.btn-load-position[data-id_position="' + positionId + '"]').click();


                    }
                }
            });

            btnTrash.on('click', function(){
                if($('#' + rowId).html() !== '' && rowId){
                    $('#' + rowId).removeClass('element-selected');
                    emptyRow(rowId, boardEventId);
                    resetVars();
                }

            });

            $(".btn-empty-board-form").on('click', function(){

                let url = "{{ route('event.board.empty-board-ajax', ['boardEvent' => ':boardEvent']) }}";
                url = url.replace(':boardEvent', boardEventId);

                $.post(url)
                    .then(function(result, status){
                            $('#pinya div').html('');
                            loadMap(boardEventId, base);
                            loadCastellersList(positionId);
                    })
                    .fail(function(result, status){
                    });

                resetVars();
            });

            $(".btn-remove-missing-form").on('click', function(){

                var attendanceTypeEvent = document.getElementById("attendanceTypeEvent");
                var attendanceTypeEventValue = attendanceTypeEvent.value;

                var attendanceStatusEvent = document.getElementById("attendanceStatusEvent");
                var attendanceStatusEventValue = attendanceStatusEvent.value;

                let url = "{{ route('event.board.remove-missing-ajax', ['boardEvent' => ':boardEvent']) }}";
                url = url.replace(':boardEvent', boardEventId);

               $.post(url,
                        {
                            attendanceType: attendanceTypeEventValue,
                            attendanceStatus: attendanceStatusEventValue,
                        }
                    )
                    .then(function(result, status){

                            result.forEach(function(e, i) {
                                $('#' + e.row.div_id).html('');
                            });
                            loadCastellersList(positionId);
                    })
                    .fail(function(result, status){
                    });

                resetVars();
            });

            $("#pinya div").on('mouseenter', function(event) {
                if (firstPointerType === 'mouse') {

                    $('div').popover('hide');
                    let el = $(this);
                    if(el.text() != ''){
                        let url = "{{ route('event.board.casteller-info', ['boardEvent' => $boardEvent->getId(), 'divId' => ':divId', 'base' => ':base']) }}";
                        url = url.replace(':base', base);

                        let timeout = setTimeout(function() {
                            url = url.replace(':divId', el.attr('id'));

                            if(el.html()){
                                $.get(url).done(function(result) {
                                    el.popover({
                                        html: true,
                                        content:
                                            '<img class="img-avatar" src="' + result.castellerPhoto + '" alt="no photo">' +
                                            '<br>' + result.castellerStatus +
                                            '<br>' + result.castellerStatusVerified +
                                            '<br>' + result.castellerHeight +
                                            '<br>' + result.castellerShoulderHeight,
                                        title: result.castellerName
                                    }).popover('show');
                                })
                            }

                        }, 1000);
                        el.on('mouseleave', function() {
                            clearTimeout(timeout);
                            el.popover('dispose');
                        });
                    }
                }
            });

            $("#positionRows").on('mouseleave', '.btn', function(){
                $(this).tooltip('hide');
            });

            $('#attachBoardOnEvent').on('click', function() {
                $(this).tooltip('hide');
                $('#modalAttachBoardOnEvent').modal('show');
            });

            $('#emptyBoard').on('click', function() {
                $(this).tooltip('hide');
                $('#modalEmptyBoard').modal('show');
            });

            $('#removeMissingCastellers').on('click', function() {
                $(this).tooltip('hide');
                $('#modalRemoveMissingCastellers').modal('show');
            });

            $('#importBoardOnEvent').on('click', function() {
                $(this).tooltip('hide');
                $('#modalImportBoardOnEvent').modal('show');
            });

            $('#editBoardEvent').on('click', function() {
                $(this).tooltip('hide');
                $('#modalEditBoardEvent').modal('show');
            });

            $("#toDisplay").on('click', function(e){
                $(this).tooltip('hide');

                e.preventDefault();
                e.stopPropagation();

                $.post( "{{ route('event.board.to-display') }}", { id: boardEventId})
                    .done(function(data) {
                        if(data === true) {
                            $("#iDisplay").addClass('text-warning fa-eye');
                            $("#iDisplay").removeClass('fa-eye-slash');
                        } else {
                            $("#iDisplay").addClass('fa-eye-slash');
                            $("#iDisplay").removeClass('text-warning fa-eye');
                        }

                    });
            });

            $("#addFavourite").on('click', function(e){
                $(this).tooltip('hide');

                e.preventDefault();
                e.stopPropagation();

                $.post( "{{ route('event.board.add-favourite') }}", { id: boardEventId})
                    .done(function(data) {
                        if(data === true) {
                            $("#iFavourite").addClass('text-warning fa-star');
                            $("#iFavourite").removeClass('fa-star-o');
                        } else {
                            $("#iFavourite").addClass('fa-star-o');
                            $("#iFavourite").removeClass('text-warning fa-star');
                        }
                    });
            });

            $("#listBoardsGroup").on('click', function (e) {
                $(this).tooltip('hide');
            });

            $("#listboard").on('click', '.btn-delete-boardevent', function (e)
            {
                e.preventDefault();

                var id_boardevent = $(this).data().id_boardevent;

                $('#deleteModal').modal('show');

                var action="{{ route('event.board.destroy', ['boardEvent' => ':id_boardevent']) }}";
                action = action.replace(':id_boardevent', id_boardevent);

                $('#formDelBoardEvent').attr('action', action);


            });

        });

    </script>
@endsection
