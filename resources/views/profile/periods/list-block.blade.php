
<div class="block-header">
    <div class="block-title">
        <h3 class="block-title"><b>{!! trans('period.periods') !!}</b></h3>
    </div>
</div>
    <div class="block-content block-content-full">
        <div class="row">
            <div class="col-md-12 text-right" style="margin-bottom: 20px;">
                @can('edit colla')
                    <button class="btn btn-primary btn-add-period"><i class="fa fa-plus-circle"></i> {!! trans('period.add_period') !!}</button>
                @endcan

            </div>

            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="periods-table">
                        <thead>
                        <tr>
                            <th>{!! trans('general.name') !!}</th>
                            <th>{!! trans('period.start_period') !!}</th>
                            <th>{!! trans('period.end_period') !!}</th>
                            <th>#</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($periods as $period)
                            <tr>
                                <td>{!! $period->getName() !!}</td>
                                <td>{!! $period->getStartPeriod() !!} </td>
                                <td>{!! $period->getEndPeriod() !!}</td>
                                <td>
                                @can('edit colla')
                                    @if($period->getId())<button class="btn btn-warning btn-edit-period" data-id_period="{!! $period->getId() !!}"><i class="fa fa-pencil"></i></button>@endif
                                    <button class="btn btn-danger btn-delete-period" data-id_period="{!! $period->id_period !!}"><i class="fa fa-trash"></i></button>
                                @endcan
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- START - Modal Update Period -->
<div class="modal fade" id="modalUpdatePeriod" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <div class="modal-content" id="modalUpdatePeriodContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>
<!-- END - Modal Update Period -->

<!-- START - Modal Add Period -->
<div class="modal fade" id="modalAddPeriod" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <div class="modal-content" id="modalAddPeriodContent">
            <!-- MODAL CONTENT -->
        </div>
    </div>
</div>
<!-- END - Modal Add Period -->

<!-- START - MODAL DELETE -->
<div class="modal fade" id="modalDelPeriod" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-popin" role="document">
        <div class="modal-content">
            {!! Form::open(array('url' => 'n', 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formDelPeriod')) !!}
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">{!! trans('period.del_period') !!}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>

                <div class="block-content text-center">
                    <i class="fa fa-warning" style="font-size: 46px;"></i>
                    <h3 class="semibold modal-title text-danger">{!! trans('general.caution') !!}</h3>
                    <p class="text-muted">{!! trans('period.del_period_warning') !!}</p>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::submit(trans('general.delete') , array('class' => 'btn btn-danger')) !!}
                <button type="button" class="btn  btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</button>
            </div>
            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!--/ END - MODAL DELETE -->

