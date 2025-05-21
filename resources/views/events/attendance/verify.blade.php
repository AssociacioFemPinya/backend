@extends('template.verify')
@section('css_before')
<link href="{{ asset('css/modals/verify-attendance.css') }}" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/mobile-detect/1.4.5/mobile-detect.min.js"></script>
<style>

</style>
@endsection

@section('content')
<div class="container">
    <div class="text-center">
        <h1 class="mb-4">{{ $event->getName() }}</h1>
        <h4 class="mb-4">{{ $event->getStartDate() -> format('d-m-Y, H:i') }}</h4>


        <!-- TOTP Container -->
        <div class="border border-success text-center bg-light rounded p-4 mb-4 shadow">
            <h4>{{ __('tokentotp.verification_code') }}</h4>
            <div class="totp-code" id="totpCode">{{ $totpCode }}</div>

            @if($totalSeconds > 0)
                <p>{{ __('tokentotp.code_changes', ['seconds' => $totalSeconds]) }}</p>
                <div class="d-flex justify-content-center align-items-center mt-3">
                    <div class="totp-timer-bar">
                        <div class="totp-timer-progress" id="timerProgress" style="width: {{ ($remainingSeconds / $totalSeconds) * 100 }}%;"></div>
                    </div>
                    <span class="font-monospace fs-5 text-secondary" style="min-width: 40px;" id="timerSeconds">{{ $remainingSeconds }}s</span>
                </div>
            @else
                <p>{{ __('tokentotp.code_static') }}</p>
            @endif
        </div>

    </div>
</div>
@endsection

@section('js')
@if($totalSeconds > 0)
<script>
    let remainingSeconds = {{ $remainingSeconds }};
    let totalSeconds = {{ $totalSeconds }};

    function updateTimer() {
        remainingSeconds--;

        document.getElementById('timerProgress').style.width = ((remainingSeconds / totalSeconds) * 100) + '%';
        document.getElementById('timerSeconds').textContent = remainingSeconds + 's';

        if (remainingSeconds <= 0) {
            location.reload();
        }
    }

    setInterval(updateTimer, 1000);
</script>
@endif
@endsection
