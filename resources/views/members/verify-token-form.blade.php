@extends('members.template.main')
@section('title', __('tokentotp.verify_attendance'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="text-center">
                    <h3 class="block-title">{{ __('tokentotp.verify_attendance_to_event') }}</h3>
                </div>
                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('member.verify.token') }}" class="text-center">
                        @csrf
                        <div class="form-group row mb-4 justify-content-center">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-calendar-check"></i>
                                    </span>
                                    <select id="event_id" name="event_id" class="form-select @error('event_id') is-invalid @enderror" required>
                                        <option value="">{{ __('tokentotp.select_event') }}</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event->getId() }}">{{ $event->getName() }} ({{ date('d/m/Y', strtotime($event->getStartDate())) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('event_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4 justify-content-center">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input id="token" type="number"
                                        class="form-control @error('token') is-invalid @enderror"
                                        name="token"
                                        value="{{ old('token') }}"
                                        required
                                        autocomplete="off"
                                        autofocus
                                        maxlength="6"
                                        oninput="this.value=this.value.slice(0,6)"
                                        placeholder="{{ __('tokentotp.enter_code') }}">
                                </div>
                                @error('token')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-0 justify-content-center">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('tokentotp.verify_button') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('token').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');

        if (e.target.value.length > 6) {
            e.target.value = e.target.value.substring(0, 6);
        }
    });
</script>
@endpush
