
<div class="block block-themed block-transparent mb-0">
    <div class="block-header bg-primary-dark">
        <h3 class="block-title">
            {!! trans('boards.template').': <b>'.$board->name.'</b>' !!}
        </h3>
        <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                <i class="si si-close"></i>
            </button>
        </div>
    </div>

    <div class="block-content">
    {!! Form::open(array('id' => 'FormChangeBoardName', 'url' => route('boards.update.name', $board->id_board), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
        <div class="row form-group">
            <div class="col-md-1">
                <label class="control-label" style="padding-top: 5px;">{!! trans('general.name') !!}</label>
            </div>
            <div class="col-md-5">
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <button type="submit" form="FormChangeBoardName" class="btn btn-alt-primary">{!! trans('general.update') !!}</button>
        </div>
        {!! Form::close() !!}
        <div class="row">
            <div class="col-md-12">
                <div class="block">
                    <ul class="nav nav-tabs nav-tabs-block" data-toggle="tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#tab-pinya" data-toggle="tab">{!! trans('general.pinya') !!}</a>
                        </li>
                        @if($board->hasFolre())
                            <li class="nav-item">
                                <a class="nav-link" href="#tab-folre" data-toggle="tab">{!! trans('general.folre') !!}</a>
                            </li>
                        @endif
                        @if($board->hasManilles())
                            <li class="nav-item">
                                <a class="nav-link" href="#tab-manilles" data-toggle="tab">{!! trans('general.manilles') !!}</a>
                            </li>
                        @endif
                        @if($board->hasPuntals())
                            <li class="nav-item">
                                <a class="nav-link" href="#tab-puntals" data-toggle="tab">{!! trans('general.puntals') !!}</a>
                            </li>
                        @endif
                    </ul>
                    <div class="block-content tab-content">
                        @if($board->hasReady())
                            <div class="tab-pane active" id="tab-pinya" role="tabpanel">
                                <div class="result_pinya" id="result-pinya" style="position: relative; height: 1200px;">
                                    {!! $board->getHtmlPinya() !!}
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <a href="{{ asset('media/colles/'.$colla->getShortName().'/svg/'.$board->getData()['pinya']['svg']) }}" target="_blank" class="btn btn-link"><i class="fa fa-download pr-5"></i>{!! trans('boards.download_svg_base', ['base' => \App\Enums\BasesEnum::Pinya()->value()]) !!}</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($board->hasFolre())
                            <div class="tab-pane" id="tab-folre" role="tabpanel">
                                <div class="result_pinya" style="position: relative; height: 1200px;">

                                    @if($board->hasReady(\App\Enums\BasesEnum::FOLRE))
                                        {!! $board->getHtmlFolre() !!}
                                    @endif

                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @if($board->hasReady(\App\Enums\BasesEnum::FOLRE))
                                            <a href="{{ asset('media/colles/'.$colla->getShortName().'/svg/'.$board->getData()['folre']['svg']) }}" target="_blank" class="btn btn-link"><i class="fa fa-download pr-5"></i>{!! trans('boards.download_svg_base', ['base' => \App\Enums\BasesEnum::Folre()->value()]) !!}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($board->hasManilles() && $board->hasReady(\App\Enums\BasesEnum::MANILLES))
                            <div class="tab-pane" id="tab-manilles" role="tabpanel">
                                <div class="result_pinya" style="position: relative; height: 1200px;">
                                    {!! $board->getHtmlManilles() !!}
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <a href="{{ asset('media/colles/'.$colla->getShortName().'/svg/'.$board->getData()['manilles']['svg']) }}" target="_blank" class="btn btn-link"><i class="fa fa-download pr-5"></i>{!! trans('boards.download_svg_base', ['base' => \App\Enums\BasesEnum::Manilles()->value()]) !!}</a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($board->hasPuntals() && $board->hasReady(\App\Enums\BasesEnum::PUNTALS))
                            <div class="tab-pane" id="tab-puntals" role="tabpanel">
                                <div class="result_pinya" style="position: relative; height: 1200px;">
                                    {!! $board->getHtmlPuntals() !!}
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <a href="{{ asset('media/colles/'.$colla->getShortName().'/svg/'.$board->getData()['puntals']['svg']) }}" target="_blank" class="btn btn-link"><i class="fa fa-download pr-5"></i>{!! trans('boards.download_svg_base', ['base' => \App\Enums\BasesEnum::Puntals()->value()]) !!}</a>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <a type="button" class="btn btn-alt-secondary" data-dismiss="modal">{!! trans('general.close') !!}</a>
    </div>
</div>

