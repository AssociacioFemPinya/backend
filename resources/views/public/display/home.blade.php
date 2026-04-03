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

        /* TOTP Overlay Styles */
        #totp-overlay {
            --totp-scale: 1;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            min-width: calc(320px * var(--totp-scale));
            max-width: calc(400px * var(--totp-scale));
            transition: opacity 0.2s, box-shadow 0.2s;
            cursor: move;
        }

        #totp-overlay .totp-overlay-card {
            padding: calc(1.5rem * var(--totp-scale)) !important;
        }

        #totp-overlay .event-name {
            font-size: calc(1.1rem * var(--totp-scale));
            font-weight: 600;
            margin-bottom: calc(4px * var(--totp-scale));
            user-select: none;
        }

        #totp-overlay.ui-draggable-dragging {
            opacity: 0.85;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4) !important;
        }

        #totp-overlay .event-datetime {
            font-size: calc(1rem * var(--totp-scale));
            color: #6c757d;
            margin-bottom: calc(12px * var(--totp-scale));
        }

        #totp-overlay .totp-title {
            font-size: calc(1.25rem * var(--totp-scale));
            margin-bottom: calc(1rem * var(--totp-scale)) !important;
        }

        #totp-overlay .totp-code {
            font-family: 'Courier New', monospace;
            font-size: calc(3rem * var(--totp-scale));
            font-weight: bold;
            letter-spacing: calc(5px * var(--totp-scale));
            color: #333;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            user-select: none;
        }

        #totp-overlay .timer-description {
            font-size: calc(0.9rem * var(--totp-scale));
            margin-top: calc(8px * var(--totp-scale));
            margin-bottom: calc(12px * var(--totp-scale));
        }

        #totp-overlay .totp-timer-bar {
            height: calc(10px * var(--totp-scale));
            background-color: #ddd;
            border-radius: calc(5px * var(--totp-scale));
            width: 100%;
            max-width: calc(300px * var(--totp-scale));
            overflow: hidden;
            margin-right: calc(10px * var(--totp-scale));
        }

        #totp-overlay .totp-timer-progress {
            height: 100%;
            background-color: #28a745;
            border-radius: calc(5px * var(--totp-scale));
            transition: width 1s linear;
        }

        #totp-overlay .timer-seconds {
            min-width: calc(40px * var(--totp-scale));
            font-size: calc(1.25rem * var(--totp-scale));
        }

        /* TOTP Toggle Button */
        #toggleTotpOverlay {
            transition: all 0.3s ease;
        }

        #toggleTotpOverlay:hover {
            transform: scale(1.05);
        }

        #toggleTotpOverlay i {
            font-size: 16px;
        }



    </style>

@endsection

@section('content')

    <!-- TOTP Overlay -->
    @if($event && $totpCode !== null)
    <div id="totp-overlay">
        <div class="totp-overlay-card border border-success text-center bg-light rounded p-4 shadow">
            <div class="event-name">{{ $event->getName() }}</div>
            <div class="event-datetime">{{ $event->getStartDate()->format('d-m-Y, H:i') }}</div>
            
            <h5 class="totp-title mb-3">{{ __('tokentotp.verification_code') }}</h5>
            <div class="totp-code" id="totpCodeDisplay">{{ $totpCode }}</div>
            
            @if($totalSeconds > 0)
                <p class="timer-description">{{ __('tokentotp.code_changes', ['seconds' => $totalSeconds]) }}</p>
                <div class="d-flex justify-content-center align-items-center">
                    <div class="totp-timer-bar">
                        <div class="totp-timer-progress" id="totpTimerProgress" style="width: {{ ($remainingSeconds / $totalSeconds) * 100 }}%;"></div>
                    </div>
                    <span class="font-monospace fs-5 text-secondary timer-seconds" id="timerSeconds">{{ $remainingSeconds }}s</span>
                </div>
            @else
                <p class="mt-2">{{ __('tokentotp.code_static') }}</p>
            @endif
        </div>
    </div>
    @endif

    <div class="row no-gutters justify-content-center">
        <div class="col-12">
            <div id="pinya" class="ml-10 mt-5">{!! $base !!}</div>
        </div>
    </div>

@endsection

@section('js')

    <script src='https://unpkg.com/panzoom@9.4.0/dist/panzoom.min.js'></script>
    <script src="{{ asset('js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-ui/jquery.ui.touch-punch.min.js') }}"></script>

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

    <!-- TOTP Draggable Script -->
    @if($event && $totpCode !== null)
    <script>
        $(function() {
            const storageKey = 'totpOverlayPosition_{{ $shortName }}';
            const storageVisibilityKey = 'totpOverlayVisible_{{ $shortName }}';
            const storageScaleKey = 'totpOverlayScale_{{ $shortName }}';
            const minScale = 0.8;
            const maxScale = 1.8;
            const defaultScale = 1;
            const scaleStep = 0.1;
            const $overlay = $('#totp-overlay');
            const $toggleBtn = $('#toggleTotpOverlay');
            const $toggleIcon = $('#totpToggleIcon');
            const $sizeDownBtn = $('#totpOverlaySizeDown');
            const $sizeUpBtn = $('#totpOverlaySizeUp');

            function clampScale(scale) {
                return Math.min(maxScale, Math.max(minScale, scale));
            }

            function getScaleState() {
                const saved = localStorage.getItem(storageScaleKey);
                if (saved === null) {
                    return defaultScale;
                }

                const parsed = parseFloat(saved);
                if (isNaN(parsed)) {
                    return defaultScale;
                }

                return clampScale(parsed);
            }

            function setScaleState(scale) {
                const validScale = clampScale(scale);
                $overlay.css('--totp-scale', validScale.toFixed(2));
                localStorage.setItem(storageScaleKey, validScale.toFixed(2));

                $sizeDownBtn.prop('disabled', validScale <= minScale);
                $sizeUpBtn.prop('disabled', validScale >= maxScale);
            }
            
            // Check initial visibility state
            function getVisibilityState() {
                const saved = localStorage.getItem(storageVisibilityKey);
                return saved === null ? true : saved === 'true';
            }
            
            // Toggle overlay visibility
            function toggleOverlayVisibility() {
                const isVisible = $overlay.is(':visible');
                const newState = !isVisible;
                
                if (newState) {
                    $overlay.fadeIn(300);
                    $toggleIcon.removeClass('fa-unlock').addClass('fa-lock');
                    $toggleBtn.removeClass('btn-outline-success').addClass('btn-success');
                } else {
                    $overlay.fadeOut(300);
                    $toggleIcon.removeClass('fa-lock').addClass('fa-unlock');
                    $toggleBtn.removeClass('btn-success').addClass('btn-outline-success');
                }
                
                localStorage.setItem(storageVisibilityKey, newState);
            }

            function updateScale(delta) {
                const currentScale = getScaleState();
                const nextScale = currentScale + delta;
                setScaleState(nextScale);
                restorePosition();
            }
            
            // Set initial visibility
            if (!getVisibilityState()) {
                $overlay.hide();
                $toggleIcon.removeClass('fa-lock').addClass('fa-unlock');
                $toggleBtn.removeClass('btn-success').addClass('btn-outline-success');
            }

            // Set initial scale
            setScaleState(getScaleState());
            
            // Toggle button click handler
            $toggleBtn.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleOverlayVisibility();
            });

            $sizeDownBtn.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                updateScale(-scaleStep);
            });

            $sizeUpBtn.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                updateScale(scaleStep);
            });
            
            // Convert initial bottom/right position to top/left BEFORE making it draggable
            function initializePosition() {
                const savedPosition = localStorage.getItem(storageKey);
                
                if (savedPosition) {
                    // Restore saved position
                    restorePosition();
                } else {
                    // Convert initial bottom/right to top/left
                    const currentPos = $overlay.offset();
                    $overlay.css({
                        top: currentPos.top + 'px',
                        left: currentPos.left + 'px',
                        bottom: 'auto',
                        right: 'auto'
                    });
                }
            }
            
            // Restore saved position
            function restorePosition() {
                const savedPosition = localStorage.getItem(storageKey);
                if (savedPosition) {
                    try {
                        const pos = JSON.parse(savedPosition);
                        
                        // Validate position is still within viewport
                        const windowWidth = $(window).width();
                        const windowHeight = $(window).height();
                        const overlayWidth = $overlay.outerWidth();
                        const overlayHeight = $overlay.outerHeight();
                        
                        let top = pos.top;
                        let left = pos.left;
                        
                        // Ensure overlay is visible
                        if (left < 0) left = 20;
                        if (top < 0) top = 20;
                        if (left + overlayWidth > windowWidth) left = windowWidth - overlayWidth - 20;
                        if (top + overlayHeight > windowHeight) top = windowHeight - overlayHeight - 20;
                        
                        // Apply position
                        $overlay.css({
                            top: top + 'px',
                            left: left + 'px',
                            bottom: 'auto',
                            right: 'auto'
                        });
                    } catch (e) {
                        console.error('Error restoring TOTP position:', e);
                    }
                }
            }
            
            // Initialize position first
            initializePosition();
            
            // Make overlay draggable from anywhere
            $overlay.draggable({
                containment: 'window',
                cursor: 'move',
                scroll: false,
                start: function(event, ui) {
                    // Ensure bottom and right are removed when drag starts
                    $(this).css({
                        bottom: 'auto',
                        right: 'auto'
                    });
                },
                stop: function(event, ui) {
                    // Save position to localStorage
                    const position = {
                        top: ui.position.top,
                        left: ui.position.left
                    };
                    localStorage.setItem(storageKey, JSON.stringify(position));
                }
            });

            // Prevent drag events from interfering with panzoom
            $overlay.on('mousedown touchstart', function(e) {
                e.stopPropagation();
            });

            // Revalidate position on window resize
            $(window).on('resize', function() {
                const currentPos = $overlay.position();
                if (currentPos.top !== 0 || currentPos.left !== 0) {
                    setTimeout(restorePosition, 100);
                }
            });
        });
    </script>
    @endif

    <!-- TOTP Auto-Update Script -->
    @if($event && $totpCode !== null && $totalSeconds > 0)
    <script>
        let totpRemainingSeconds = {{ $remainingSeconds }};
        let totpTotalSeconds = {{ $totalSeconds }};
        const totpRefreshUrl = "{{ route('public.display.totp-code', ['shortName' => $shortName, 'token' => $token]) }}";

        function updateTotpTimer() {
            if (totpRemainingSeconds <= 0) {
                // Fetch new code
                refreshTotpCode();
                return;
            }

            totpRemainingSeconds--;

            // Update progress bar
            const progressBar = document.getElementById('totpTimerProgress');
            if (progressBar) {
                const percentage = (totpRemainingSeconds / totpTotalSeconds) * 100;
                progressBar.style.width = percentage + '%';
            }

            // Update seconds counter
            const timerSeconds = document.getElementById('timerSeconds');
            if (timerSeconds) {
                timerSeconds.textContent = totpRemainingSeconds + 's';
            }
        }

        function refreshTotpCode() {
            $.get(totpRefreshUrl)
                .done(function(data) {
                    // Update code display
                    $('#totpCodeDisplay').text(data.totpCode);
                    
                    // Reset timer
                    totpRemainingSeconds = data.remainingSeconds;
                    totpTotalSeconds = data.totalSeconds;
                    
                    // Update progress bar to 100%
                    $('#totpTimerProgress').css('width', '100%');
                    
                    // Update seconds counter
                    $('#timerSeconds').text(data.remainingSeconds + 's');
                })
                .fail(function(error) {
                    console.error('Error refreshing TOTP code:', error);
                });
        }

        // Update timer every second
        setInterval(updateTotpTimer, 1000);
    </script>
    @endif

    <script type="text/javascript">let colla_shortname = '{{ $shortName }}';</script>
    <script src="{{ asset('js/pages/listen_display_channel.js') }}"></script>
@endsection
