@extends('template.main')

@section('title', ucfirst(trans('rondes.rondes')))
@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/rowReorder.dataTables.min.css') }}">
    <style type="text/css">
        #rondes .buttons, #pinyes .buttons {
            padding:0 !important;
            text-align: center;
            vertical-align: middle;
        }

        .even.add-row {
            animation-name: add-row-even;
            animation-duration: 1s;
            animation-timing-function: ease;
            animation-iteration-count: 1;
        }

        .odd.add-row {
            animation-name: add-row-even;
            animation-duration: 1s;
            animation-timing-function: ease;
            animation-iteration-count: 1;
        }

        .delete-row {
            animation-name: delete-row;
            animation-duration: 1s;
            animation-timing-function: ease;
            animation-iteration-count: 1;
        }

        @keyframes add-row-even {
            from {
                background-color: rgb(156, 204, 101);

                color:#000;
            }
            to {
                background-color: #fff;

                color: rgb(87, 87, 87);
            }
        }

        @keyframes add-row-odd {
            from {
                background-color: rgb(156, 204, 101);
                font-weight: bolder;
                color:#000;
            }
            to {
                background-color: rgba(0, 0, 0, 0.02);
                font-weight: unset;
                color: rgb(87, 87, 87);

            }
        }

        @keyframes delete-row {
            from {
                background-color: red;
                color:#000;
            }
            to {
                background-color: #fff;
                color: rgb(87, 87, 87);
            }
        }

    </style>
@endsection

@section('content')

    <div class="block">

        <div class="block-header block-header-default">
            <div class="block-title">
                <h3 class="block-title"><b>{!! ucfirst(trans('rondes.rondes')) !!}:</b> {!! $event->getName() !!} <span class="text-muted">({!! \App\Helpers\Humans::readEventColumn($event, 'start_date'); !!})</span></h3>
            </div>
        </div>

        <div class="block-content block-content-full">
            <div class="row" style="padding-top: 25px;">
                <div class="col-md-6">
                    <h3 class="block-title"><b>{!! ucfirst(trans('rondes.rondes')) !!}</b></h3>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" style="width: 100%;" id="rondes">
                            <thead>
                            <tr>
                                <th></th>
                                <th>{!! trans('rondes.ronda') !!}</th>
                                <th>{!! trans('boards.pinya') !!}</th>
                                <th>{!! trans('boards.template') !!}</th>
                                <th class="buttons"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rondes as $ronda)
                                <?php $pinya = $ronda->getBoardEvent() ?>
                                <tr>
                                    <td></td>
                                    <td id="ronda_num">{!! $ronda->getRonda() !!}</td>
                                    <td>{!! $pinya->getDisplayName() !!}</td>
                                    <td>{!! $pinya->getBoard()->getName() !!}</td>
                                    <td class="buttons">
                                        <button type="button" class="btn btn-sm btn-warning btn-edit-pinya mr-5" data-id_pinya='{{ $pinya->getId()}}' data-toggle="tooltip" data-placement="bottom" title="{!! trans('boards.tooltip_edit') !!}"><i class="fa fa-pencil"></i></button>
                                        <a href="{{ route('event.board', ['event' => $pinya->getEvent()->getId(), 'boardEvent' => $pinya->getId()]) }} " class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="bottom" title="{!! trans('rondes.tooltip_editor') !!}"><img src=" {!! asset('media/img/ico_pinya_o3.svg') !!}" style="width: 15px;" alt=""></a>
                                        <button type="button" class="btn btn-circle btn-dual-secondary btn-delete-ronda" data-id_ronda='{{ $ronda->getId()}}' data-toggle="tooltip" data-placement="bottom" title="{!! trans('rondes.tooltip_remove_ronda') !!}"><i class="fa fa-arrow-right"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                <h3 class="block-title"><b>{!! ucfirst(trans('boards.available_pinyes')) !!}</b></h3>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" style="width: 100%;" id="pinyes">
                        <thead>
                        <tr>
                            <th class="buttons"></th>
                            <th>{!! trans('general.name') !!}</th>
                            <th>{!! trans('boards.template') !!}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pinyes as $pinya)
                            <tr>
                                <td class="buttons">
                                    <button class="btn btn-circle btn-dual-secondary mr-5" id="addRonda" data-id_pinya="{{ $pinya->getId() }}" data-toggle="tooltip" data-placement="bottom" title="{!! trans('rondes.tooltip_add_ronda') !!}"><i class="fa fa-arrow-left"></i></button>
                                    <button class="btn btn-sm btn-warning btn-edit-pinya mr-5" data-id_pinya='{{ $pinya->getId()}}' data-toggle="tooltip" data-placement="bottom" title="{!! trans('boards.tooltip_edit') !!}"><i class="fa fa-pencil"></i></button>
                                    <a href="{{ route('event.board', ['event' => $pinya->getEvent()->getId(), 'boardEvent' => $pinya->getId()]) }} " class="btn btn-sm btn-primary mr-5" data-toggle="tooltip" data-placement="bottom" title="{!! trans('rondes.tooltip_editor') !!}"><img src=" {!! asset('media/img/ico_pinya_o3.svg') !!}" style="width: 15px;" alt=""></a>
                                </td>
                                <td>{!! $pinya->getName() !!}</td>
                                <td>{!! $pinya->getBoard()->getName() !!}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            </div>
        </div>
    </div>
    <!-- START - Modal long -->
    <div class="modal fade" id="modalEditBoardEvent" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popin" role="document">
            <div class="modal-content" id="modalEditBoardEventContent">
                <!-- MODAL CONTENT -->
            </div>
        </div>
    </div>
    <!-- END - Modal long -->

@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.rowReorder.min.js') }}"></script>


    <!-- Page JS Code -->
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


            let rondes = $("#rondes");
            let pinyes = $("#pinyes");

            function drawRondesTable() {
                rondes = $("#rondes").DataTable({
                    "language": {!! trans('datatables.translation') !!},
                    "stateSave": true,
                    "stateDuration": -1,
                    "searching": false,
                    "paging": false,
                    "info": false,
                    "responsive": true,
                    "scrollX": false,
                    "rowReorder": {
                        snapX: 10,
                        dataSrc: "ronda",
                        update: true,
                    },
                    columnDefs: [
                        {
                            className: 'reorder',
                            render: () => '≡',
                            targets: 0
                        },
                        {orderable: false, targets: '_all'},
                        {"width": "10%", "targets": 0},
                        {"width": "10%", "targets": 1},
                        {"width": "140px", "targets": 4},
                    ],
                    order: [[1, 'asc']],
                    "columns": [
                        {data: "hamb"},
                        {data: "ronda"},
                        {data: "pinya"},
                        {data: "template"},
                        {data: "#"},
                    ],
                    "drawCallback": function( settings ) {

                    }
                });
            }

            function drawPinyesTable() {
                pinyes = $("#pinyes").DataTable({
                    "language": {!! trans('datatables.translation') !!},
                    "stateSave": true,
                    "stateDuration": -1,
                    "searching": false,
                    "paging": false,
                    "info": false,
                    "responsive": true,
                    "ordering": true,
                    scrollX: false,
                    order: [[1, 'asc']],
                    columnDefs: [
                        {"width": "140px", "targets": 0},
                    ],
                    "columns": [
                        {"data": "#", "orderable": false},
                        {"data": "pinya"},
                        {"data": "template"},
                    ],
                });
            }

            $('#pinyes').on('click', '#addRonda', function(e) {

                $('[data-toggle="tooltip"]').tooltip('hide');

                e.preventDefault();


                let pinyaButton = $(this);
                let id_pinya = $(this).data('id_pinya');
                let row = pinyes.row(pinyaButton.parents('tr'));
                let pinya = pinyaButton.parents('tr');

                $.post( "{{ route('event.rondes.add-ronda-ajax', ['event' => $event]) }}",
                    {
                        id_pinya: id_pinya,
                    }
                )
                .done(function (result, status)
                    {
                        $(':button').prop('disabled', true);

                        var rowNode = rondes
                            .row.add( {
                                "hamb":     "",
                                "ronda":    result.ronda,
                                "pinya":    result.pinya,
                                "template": result.template,
                                "#":        result.buttons,
                            })
                            .draw()
                            .node();

                        $( rowNode ).addClass('add-row');
                        $( rowNode ).find('td:last').addClass('buttons');

                        pinya.addClass('delete-row');


                        setTimeout(function () {
                            $( rowNode ).removeClass('add-row');
                            row.remove().draw();
                            $(':button').prop('disabled', false);
                            $('[data-toggle="tooltip"]').tooltip();

                        }, 1000);

                    }
                )
                .fail(function (result, status) {
                    alert("failed")
                });
            });

            $("#rondes").on('click', '.btn-delete-ronda', function (e)
            {
                e.preventDefault();
                $('[data-toggle="tooltip"]').tooltip('hide');


                let rondaButton = $(this);
                let id_ronda = $(this).data("id_ronda");
                let url = "{{ route('event.rondes.destroy-ronda-ajax', ['ronda' => ':id_ronda']) }}";
                url = url.replace(':id_ronda', id_ronda);

                $.post(url)
                .done(function (result, status) {

                        $(':button').prop('disabled', true);

                        let row = rondes.row(rondaButton.parents('tr'));
                        let ronda = rondaButton.parents('tr');

                        let rowNode = pinyes
                            .row.add({
                                "#": result.buttons,
                                "pinya": result.displayName,
                                "template": result.template,
                            })
                            .draw()
                            .node()

                        ronda.addClass('delete-row');
                        $( rowNode ).addClass('add-row');
                        $( rowNode ).find('td:first').addClass('buttons');

                        setTimeout(function () {
                            $(rowNode).removeClass('add-row');
                            row.remove().draw();
                            rondesTableReorder();
                            $(':button').prop('disabled', false);
                            $('[data-toggle="tooltip"]').tooltip();
                        }, 1000);

                    }
                )
                .fail(function (result, status) {
                    alert("failed")
                });

            });

            drawRondesTable();
            drawPinyesTable();

            $('[data-toggle="tooltip"]').tooltip();


            rondes.on('row-reordered.dt', function (e, details, edit) {
                $('[data-toggle="tooltip"]').tooltip('hide');

                for(let i = 0; i < details.length; i++){
                    let ronda_num = details[i].newPosition+1;
                    let rowNode = details[i].node;
                    let id_ronda = $( rowNode ).find('button.btn-delete-ronda').data("id_ronda");
                    updateRonda(ronda_num,id_ronda);
                }
                $('[data-toggle="tooltip"]').tooltip();

            });

            function rondesTableReorder(){
                $('[data-toggle="tooltip"]').tooltip('hide');

                rondes.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                    let data = this.data();
                    data.ronda = rowLoop+1;
                    rondes
                        .row( this )
                        .data( data)
                        .draw();
                    let rowNode = this.node();
                    let id_ronda = $( rowNode ).find('button.btn-delete-ronda').data("id_ronda");
                    updateRonda(rowLoop+1,id_ronda);
                });
                $('[data-toggle="tooltip"]').tooltip();

            }

            function updateRonda(ronda_num,id_ronda){
                $('[data-toggle="tooltip"]').tooltip('hide');

                let url = "{{ route('event.rondes.update-ronda-ajax', ['ronda' => ':id_ronda']) }}";
                url = url.replace(':id_ronda', id_ronda);

                $.post(url,
                    {
                        ronda_num: ronda_num,
                    }
                )
                .fail(function (result, status) {
                    alert("ERROR")
                });
            }


            $('.btn-edit-pinya').on('click', function (event)
            {
                $('[data-toggle="tooltip"]').tooltip('hide');

                let id_pinya = $(this).data().id_pinya;

                $('#modalEditBoardEvent').modal('show');

                $('#modalEditBoardEventContent').html('<div class="col-md-12 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                let url = "{{ route('event.board.edit-board-event-modal', ['boardEvent' => ':id_pinya', 'fromRondes' => 1]) }}";
                url = url.replace(':id_pinya',id_pinya);
                console.log(url);

                $.get( url, function( data ) {
                    $('#modalEditBoardEventContent').html( data );
                });

            });

        });
    </script>
@endsection
