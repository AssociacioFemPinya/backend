@extends('template.verify')
@section('title', __('attendance.verify_touch_title'))
@section('css_before')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-keyboard@latest/build/css/index.css">
<style>
    body, html {
        margin: 0;
        padding: 0;
        height: 100vh;
        height: 100dvh;
        overflow: hidden; /* No scroll at the document level */
        background-color: #f4f6fa;
    }
    #page-container {
        height: 100vh;
        height: 100dvh;
        display: flex;
        flex-direction: column;
    }
    #page-footer {
        display: none !important;
    }
    .touch-container {
        display: flex;
        flex-direction: column;
        height: 100vh;
        height: 100dvh;
        max-height: 100vh;
        max-height: 100dvh;
        padding: 15px;
    }
    .search-section {
        flex: 0 0 auto;
        margin-bottom: 15px;
    }
    .search-input {
        width: 100%;
        font-size: 2rem;
        padding: 15px;
        border-radius: 10px;
        border: 2px solid #ddd;
        text-align: center;
        background: #fff;
    }
    .results-section {
        flex: 1 1 auto;
        overflow-y: auto; /* Only this section scrolls */
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 15px;
        padding: 10px;
    }
    .keyboard-section {
        flex: 0 0 auto;
    }
    
    /* Bigger keyboard keys for tablets, responsive to viewport */
    .hg-theme-default .hg-button {
        height: clamp(35px, 10vh, 60px);
        font-size: clamp(1rem, 3vh, 1.5rem);
    }
    
    .casteller-item {
        font-size: 1.8rem;
        padding: 20px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        transition: background 0.1s;
    }
    .casteller-item:hover, .casteller-item:active {
        background: #e6f7ff;
    }
    .casteller-alias {
        font-weight: bold;
        color: #007bff;
    }
    .casteller-name {
        color: #6c757d;
        font-size: 1.4rem;
        margin-left: 10px;
    }

    /* Adjust layout for landscape mode on small devices */
    @media (max-height: 500px) {
        .touch-container {
            padding: 5px;
        }
        .search-section {
            margin-bottom: 5px;
        }
        .search-input {
            padding: 5px;
            font-size: 1.2rem;
        }
        .results-section {
            margin-bottom: 5px;
            padding: 5px;
        }
        .casteller-item {
            padding: 10px;
            font-size: 1.2rem;
        }
        .casteller-name {
            font-size: 1rem;
        }
    }

</style>
@endsection

@section('content')
<div class="touch-container">
    <div class="search-section">
        <input type="text" class="search-input input" readonly />
    </div>
    
    <div class="results-section">
        <div id="castellers-list">
            <!-- Iterated via JS -->
        </div>
    </div>
    
    <div class="keyboard-section">
        <div class="simple-keyboard"></div>
    </div>
</div>

<!-- Modal Confirm -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('attendance.verify_touch_title') }}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" style="padding: 40px;">
                <h2 id="modal-casteller-name"></h2>
                <input type="hidden" id="modal-casteller-id" />
                <p class="font-size-h4 mt-20">{{ __('attendance.verify_touch_confirm') }}</p>
                <p class="font-size-h5 text-muted">{{ $event->getName() }}</p>
            </div>
            <div class="modal-footer d-flex justify-content-between" style="padding: 20px;">
                <button type="button" class="btn btn-secondary btn-lg" style="font-size: 1.5rem; padding: 15px 30px;" data-dismiss="modal">{{ __('attendance.verify_touch_cancel') }}</button>
                <button type="button" class="btn btn-success btn-lg" style="font-size: 1.5rem; padding: 15px 30px;" id="btn-confirm-verify">{{ __('attendance.verify_touch_yes') }} <i class="fa fa-check"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-keyboard@latest/build/index.min.js"></script>
<script>
    const castellers = {!! json_encode($castellers) !!};
    const eventId = {{ $event->getId() }};
    
    $(document).ready(function() {
        
        const Keyboard = window.SimpleKeyboard.default;
        const inputElement = document.querySelector(".input");

        const myKeyboard = new Keyboard({
            onChange: input => onChange(input),
            onKeyPress: button => onKeyPress(button),
            theme: "hg-theme-default hg-layout-default",
            layout: {
                default: [
                    "1 2 3 4 5 6 7 8 9 0 {bksp}",
                    "q w e r t y u i o p",
                    "a s d f g h j k l n",
                    "z x c v b m , .",
                    "{space}"
                ]
            },
            display: {
                "{bksp}": "{{ __('attendance.verify_touch_backspace') }}",
                "{enter}": "{{ __('attendance.verify_touch_enter') }}",
                "{space}": "{{ __('attendance.verify_touch_space') }}"
            }
        });

        function onChange(input) {
            inputElement.value = input;
            renderCastellers(input);
        }

        function onKeyPress(button) {
            // Optional: handle specific keys
        }
        
        function normalizeString(str) {
            if (!str) return "";
            return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
        }

        function renderCastellers(query) {
            const listEl = document.getElementById('castellers-list');
            listEl.innerHTML = '';
            
            const normalizedQuery = normalizeString(query);
            if (normalizedQuery.trim().length < 3) {
                listEl.innerHTML = '<div class="text-center p-20 text-muted"><h4>{{ __("attendance.verify_touch_start_typing") }}</h4></div>';
                return;
            }
            
            let filtered = castellers.filter(c => {
                const matchAlias = normalizeString(c.alias).includes(normalizedQuery);
                const matchName = normalizeString(c.name).includes(normalizedQuery);
                const matchLastName = normalizeString(c.last_name).includes(normalizedQuery);
                return matchAlias || matchName || matchLastName;
            });

            if (filtered.length === 0) {
                listEl.innerHTML = '<div class="text-center p-20 text-muted"><h4>{{ __("attendance.verify_touch_no_castellers") }} "'+query+'"</h4></div>';
                return;
            }

            // Opcional: paginar o limitar para que no explote la memoria visual.
            // Top 40 matches is more than enough for a scroll view.
            filtered.slice(0, 40).forEach(c => {
                const div = document.createElement('div');
                div.className = 'casteller-item';
                
                let displayName = c.alias ? c.alias : c.name;
                let fullName = (c.name || '') + ' ' + (c.last_name || '');
                
                div.innerHTML = `<span class="casteller-alias">${displayName}</span> <span class="casteller-name">${fullName !== displayName ? fullName : ''}</span>`;
                
                div.addEventListener('click', function() {
                    $('#modal-casteller-name').text(displayName + (fullName !== displayName ? ' (' + fullName + ')' : ''));
                    $('#modal-casteller-id').val(c.id_casteller);
                    $('#confirmModal').modal('show');
                });
                
                listEl.appendChild(div);
            });
        }
        
        // Initial render
        renderCastellers('');
        
        // Confirm Button AJAX
        $('#btn-confirm-verify').click(function() {
            var castellerId = $('#modal-casteller-id').val();
            if (!castellerId) return;
            
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __("attendance.verify_touch_verifying") }}');
            
            $.ajax({
                url: "{{ route('event.attendance.set-status-verified') }}", // same endpoint as backend list
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id_event: eventId,
                    id_casteller: castellerId,
                    status: 1
                },
                success: function(response) {
                    $('#confirmModal').modal('hide');
                    btn.prop('disabled', false).html("{{ __('attendance.verify_touch_yes') }} <i class='fa fa-check'></i>");
                    
                    // Reset input
                    myKeyboard.clearInput();
                    inputElement.value = "";
                    renderCastellers('');
                    
                    // Visual confirmation
                    Codebase.helpers('notify', {
                        align: 'center',             
                        from: 'bottom',
                        type: 'success',
                        icon: 'fa fa-check mr-5',
                        message: '{{ __("attendance.verify_touch_success") }}'
                    });
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html("{{ __('attendance.verify_touch_yes') }} <i class='fa fa-check'></i>");
                    Codebase.helpers('notify', {
                        align: 'center',             
                        from: 'bottom',
                        type: 'danger',
                        icon: 'fa fa-times mr-5',
                        message: '{{ __("attendance.verify_touch_error") }}'
                    });
                }
            });
        });
    });
</script>
@endsection
