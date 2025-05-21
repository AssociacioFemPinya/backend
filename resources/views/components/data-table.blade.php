<div class="block">

@if (!$datatable->isEmbedded())
    <div class="block-header block-header-default">
        <div class="block-title">
            <h3 class="block-title"><b>{{ $datatable->getTitle() }}</b></h3>
        </div>
    </div>
@endif

    <div class="block-content block-content-full">
        <div class="row"  style="padding-top: 25px;">
            <div class="col-md-12">
                <table  class="table table-hover table-striped table-bordered" style="width: 100%;" id={{ $datatable->getId() }}>
                    <thead>
                        <tr>
                            @foreach ($datatable->getColumns() as $column)
                            <th scope="col">{{ $column->getTitle() }}</th>
                            @endforeach
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@push('datatables_js')

<!-- Page JS Code -->
<script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables/buttons/buttons.html5.js') }}"></script>
<script src="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.js') }}"></script>

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
        $('#{{ $datatable->getId() }}').DataTable( {
            "language": {!! trans('datatables.translation') !!},
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ $datatable->getUrl() }}",
                "type": "POST",
                @if (!empty($datatable->getPostAjaxData()))
                "data": function ( d ) {
                    return $.extend( {}, d, {
                        @foreach ($datatable->getPostAjaxData() as $dataElement)
                        "{{$dataElement}}": $('#{{$dataElement}}').val(),
                        @endforeach
                    } );
                }
                @endif
            },
            "ordering":'true',
            "stateSave": true,
            "stateDuration": -1,
            "order": [{{ $datatable->getOrder() }}, '{{ $datatable->getOrderDir() }}'],
            "columns": [
            @foreach ($datatable->getColumns() as $column)
                { "data": "{{ $column->getName() }}", "name": "{{ $column->getName() }}", "orderable": "{{ $column->getOrderable() }}" },
            @endforeach
            ],
            "columnDefs": [
            @foreach ($datatable->getColumns() as $index => $column)
                @if ($column->getWidth())
                { "width": "{{ $column->getWidth() }}%", "targets": {{$index}} },
                @endif
            @endforeach
            ],
            "drawCallback": function(settings) {
                if (typeof datatablesDrawCallback === 'function') {
                    datatablesDrawCallback.call(this);
                }
            }
        } );
    } );
</script>

@endpush
