@extends('template.main')

@section('title', trans('general.attendance'))

@section('css_before')
    <style>
        .kpi-card {
            border: 0;
            border-radius: 0.75rem;
            box-shadow: 0 8px 24px rgba(29, 63, 112, 0.08);
            height: 100%;
        }

        .kpi-label {
            color: #5c6f82;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.25rem;
        }

        .kpi-value {
            color: #133c63;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            margin: 0;
        }

        .question-card {
            border: 0;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(17, 34, 68, 0.08);
            height: 100%;
        }

        .question-meta {
            color: #6b7d90;
            font-size: 0.88rem;
        }

        .chart-shell {
            height: 260px;
            position: relative;
        }

        .options-table-wrap {
            max-height: 260px;
            overflow: auto;
            border: 1px solid #e6edf8;
            border-radius: 0.5rem;
        }

        .options-table-wrap .table {
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        .options-table-wrap th {
            position: sticky;
            top: 0;
            background: #f4f8ff;
            z-index: 2;
        }

        .empty-state {
            border: 1px dashed #c7d6ea;
            border-radius: 0.75rem;
            background: #f9fbff;
        }

        @media (max-width: 767px) {
            .chart-shell {
                height: 260px;
            }

            .kpi-value {
                font-size: 1.7rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="block">
        <div class="block-header block-header-default row">
            <div class="block-title col-12 col-lg-8">
                <h3 class="block-title">
                    <b>{!! trans('attendance.dashboard_title') !!}:</b>
                    {!! $event->getName() !!}
                    <span class="text-muted">({!! \App\Helpers\Humans::readEventColumn($event, 'start_date') !!})</span>
                </h3>
            </div>
            <div class="block-options col-12 col-lg-4 text-left text-lg-right pt-3 pt-lg-0">
                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-download"></i> {{ trans('attendance.dashboard_export_attendance') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="{{ route('event.attendance.dashboard.export-aggregates-excel', $event->getId()) }}">
                            <i class="fa fa-file-excel-o mr-1"></i> Excel
                        </a>
                        <a class="dropdown-item" href="{{ route('event.attendance.dashboard.export-aggregates-ods', $event->getId()) }}">
                            <i class="fa fa-file-text-o mr-1"></i> ODS
                        </a>
                    </div>
                </div>
                <a href="{{ route('event.attendance', $event->getId()) }}" class="btn btn-alt-secondary">
                    <i class="fa fa-arrow-left"></i> {{ trans('general.back') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6 col-xl-3 mb-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <p class="kpi-label">{{ trans('attendance.dashboard_responses') }}</p>
                    <p class="kpi-value">{{ $dashboard['totals']['responses'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 mb-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <p class="kpi-label">{{ trans('attendance.dashboard_attendances') }}</p>
                    <p class="kpi-value">{{ $dashboard['totals']['registered_attendances'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 mb-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <p class="kpi-label">{{ trans('attendance.dashboard_questions') }}</p>
                    <p class="kpi-value">{{ $dashboard['totals']['questions'] }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(! $dashboard['has_schema'])
        <div class="block empty-state">
            <div class="block-content text-center py-5">
                <h4 class="mb-2">{{ trans('attendance.dashboard_empty_schema_title') }}</h4>
                <p class="text-muted mb-0">{{ trans('attendance.dashboard_empty_schema_body') }}</p>
            </div>
        </div>
    @else
        @php
            $questionsWithData = collect($dashboard['questions'])->filter(function ($q) {
                return !empty($q['labels']);
            })->values();
        @endphp

        @if($questionsWithData->isEmpty())
            <div class="block empty-state">
                <div class="block-content text-center py-5">
                    <h4 class="mb-2">{{ trans('attendance.dashboard_empty_responses_title') }}</h4>
                    <p class="text-muted mb-0">{{ trans('attendance.dashboard_empty_responses_body') }}</p>
                </div>
            </div>
        @else
            <div class="row" id="questionCharts">
                @foreach($questionsWithData as $index => $question)
                    <div class="col-12 col-xl-6 mb-4">
                        <div class="card question-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h4 class="mb-1">{{ $question['label'] }}</h4>
                                        <div class="question-meta">
                                            {{ trans('attendance.dashboard_completion') }} {{ $question['completion_rate'] }}% ({{ $question['answered'] }}/{{ $question['total'] }})
                                        </div>
                                    </div>
                                    <span class="badge badge-primary badge-pill">{{ strtoupper($question['type']) }}</span>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-lg-7 mb-3 mb-lg-0">
                                        <div class="chart-shell">
                                            <canvas id="questionChart{{ $index }}"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-5">
                                        <div class="options-table-wrap">
                                            <table class="table table-sm table-striped table-vcenter">
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans('attendance.dashboard_option') }}</th>
                                                        <th class="text-right">{{ trans('attendance.dashboard_responses') }}</th>
                                                        <th class="text-right">{{ trans('attendance.dashboard_percentage') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($question['option_rows'] as $row)
                                                        <tr>
                                                            <td>{{ $row['label'] }}</td>
                                                            <td class="text-right">{{ $row['count'] }}</td>
                                                            <td class="text-right">{{ $row['percentage'] }}%</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        $(function () {
            const questions = @json(isset($questionsWithData) ? $questionsWithData : []);
            const responsesLabel = @json(trans('attendance.dashboard_responses'));

            const palette = [
                '#0f4c81', '#2f855a', '#d97706', '#dc2626', '#7c3aed',
                '#0d9488', '#475569', '#ea580c', '#4f46e5', '#be185d'
            ];

            function pickChartType(fieldType) {
                const doughnutTypes = ['radio-group', 'select', 'checkbox-group'];
                return doughnutTypes.includes(fieldType) ? 'doughnut' : 'bar';
            }

            questions.forEach(function (question, idx) {
                const canvas = document.getElementById('questionChart' + idx);
                if (!canvas || !question.labels || question.labels.length === 0) {
                    return;
                }

                const colors = question.labels.map(function (_, i) {
                    return palette[i % palette.length];
                });

                const chartType = pickChartType(question.type);

                new Chart(canvas, {
                    type: chartType,
                    data: {
                        labels: question.labels,
                        datasets: [{
                            label: responsesLabel,
                            data: question.data,
                            backgroundColor: colors,
                            borderColor: chartType === 'bar' ? colors : '#ffffff',
                            borderWidth: chartType === 'bar' ? 1 : 2,
                            borderRadius: chartType === 'bar' ? 8 : 0,
                            maxBarThickness: 40
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: chartType === 'doughnut',
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return context.label + ': ' + context.raw;
                                    }
                                }
                            }
                        },
                        scales: chartType === 'bar' ? {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            },
                            x: {
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 35,
                                    minRotation: 0
                                }
                            }
                        } : {}
                    }
                });
            });
        });
    </script>
@endsection
