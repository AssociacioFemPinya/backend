@extends('template.public.main')

@section('title', 'FemPinya')
@section('css_after')
    <style>
        .mainLogo{
            height:auto;
            width:100%;
        }
        @media (min-width: 576px) {
            .mainLogo{
                height:100%;
                width:auto;
                max-height:400px;
            }
        }
    </style>

@endsection

@section('content')
        <div class="hero-inner">
            <div class="content content-full">
                <div class="py-30 text-center">
                    <img src="{!! $logo !!}" alt="Logo" class="mainLogo">
                </div>
            </div>
        </div>

@endsection

@section('js')

    <script src="https://unpkg.com/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>

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

    <script type="text/javascript">let colla_shortname = '{{ $shortName }}';</script>
    <script src="{{ asset('js/pages/listen_display_channel.js') }}"></script>
@endsection
