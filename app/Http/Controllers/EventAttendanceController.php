<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Casteller;
use App\Colla;
use App\Enums\AttendanceStatus;
use App\Enums\CastellersStatusEnum;
use App\Enums\FilterSearchTypesEnum;
use App\Enums\TypeTags;
use App\Event;
use App\Exports\AttendanceDashboardAggregatesExport;
use App\Helpers\Humans;
use App\Services\CsvExporter;
use App\Services\NotificationService;
use App\Services\TOTPService;
use App\Tag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class EventAttendanceController extends Controller
{
    /** List Castellers attenders at event*/
    public function getIndex(Event $event): View
    {

        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $this->authorize('getEvent', $event);

        $data_content['event'] = $event;
        $data_content['tags'] = Tag::currentTags();
        $data_content['positions'] = Tag::currentTags('POSITIONS');
        $data_content['statuses'] = Casteller::getStatuses();

        return view('events.attendance.list', $data_content);
    }

    /** Show aggregated stats dashboard for event attendance form responses */
    public function getDashboard(Event $event): View
    {
        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $this->authorize('getEvent', $event);

        $data_content['event'] = $event;
        $data_content['dashboard'] = $this->buildFormResponseStats($event);

        return view('events.attendance.dashboard', $data_content);
    }

    public function exportDashboardAggregatesExcel(Event $event)
    {
        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $this->authorize('getEvent', $event);

        $dashboard = $this->buildFormResponseStats($event);

        $headings = [
            trans('attendance.dashboard_questions'),
            trans('attendance.dashboard_option'),
            trans('attendance.dashboard_responses'),
            trans('attendance.dashboard_percentage'),
        ];

        $rows = $this->buildDashboardAggregateRows($dashboard['questions']);
        $date = now()->format('Y-m-d');

        return Excel::download(
            new AttendanceDashboardAggregatesExport($rows, $headings),
            "attendance_dashboard_{$event->getId()}_{$date}.xlsx",
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function exportDashboardAggregatesOds(Event $event)
    {
        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $this->authorize('getEvent', $event);

        $dashboard = $this->buildFormResponseStats($event);

        $headings = [
            trans('attendance.dashboard_questions'),
            trans('attendance.dashboard_option'),
            trans('attendance.dashboard_responses'),
            trans('attendance.dashboard_percentage'),
        ];

        $rows = $this->buildDashboardAggregateRows($dashboard['questions']);
        $date = now()->format('Y-m-d');

        return Excel::download(
            new AttendanceDashboardAggregatesExport($rows, $headings),
            "attendance_dashboard_{$event->getId()}_{$date}.ods",
            \Maatwebsite\Excel\Excel::ODS
        );
    }

    private function buildDashboardAggregateRows(array $questions): array
    {
        $rows = [];

        foreach ($questions as $question) {
            if (empty($question['option_rows'])) {
                continue;
            }

            foreach ($question['option_rows'] as $row) {
                $rows[] = [
                    $question['label'],
                    $row['label'],
                    $row['count'],
                    $row['percentage'].'%',
                ];
            }
        }

        return $rows;
    }

    private function buildFormResponseStats(Event $event): array
    {
        $schema = is_array($event->form_schema) ? $event->form_schema : [];
        $eligibleCastellers = Casteller::filter($event->getColla())
            ->withStatus(CastellersStatusEnum::ActiveAll())
            ->withTags($event->getCastellerTags()->pluck('id_tag')->toArray(), FilterSearchTypesEnum::OR)
            ->eloquentBuilder()
            ->select('castellers.id_casteller')
            ->get();

        $eligibleCastellerIds = $eligibleCastellers->pluck('id_casteller')->toArray();
        $eligibleCastellersCount = count($eligibleCastellerIds);

        $attendances = $event->attendances->whereIn('casteller_id', $eligibleCastellerIds);

        $questions = [];
        foreach ($schema as $field) {
            $name = $field['name'] ?? null;
            if (! $name) {
                continue;
            }

            $type = $field['type'] ?? 'text';

            // Skip non-input components included by formBuilder.
            if (in_array($type, ['header', 'paragraph', 'button'], true)) {
                continue;
            }

            $question = [
                'name' => $name,
                'label' => $field['label'] ?? $name,
                'type' => $type,
                'counts' => [],
                'value_to_label' => [],
                'answered' => 0,
            ];

            if (isset($field['values']) && is_array($field['values'])) {
                foreach ($field['values'] as $option) {
                    $optionValue = trim((string) ($option['value'] ?? ''));
                    $optionLabel = trim((string) ($option['label'] ?? $optionValue));
                    if ($optionLabel !== '') {
                        $question['counts'][$optionLabel] = 0;
                    }
                    if ($optionValue !== '' && $optionLabel !== '') {
                        $question['value_to_label'][$optionValue] = $optionLabel;
                    }
                }
            }

            $questions[$name] = $question;
        }

        $respondents = 0;
        foreach ($attendances as $attendance) {
            $options = $attendance->getOptions() ?: [];
            if (empty($options)) {
                continue;
            }

            $respondents++;

            foreach ($questions as $name => &$question) {
                if (! array_key_exists($name, $options)) {
                    continue;
                }

                $rawValue = $options[$name];
                $values = is_array($rawValue) ? $rawValue : [$rawValue];
                $hasAnswer = false;

                foreach ($values as $value) {
                    $cleanValue = trim((string) $value);
                    if ($cleanValue === '') {
                        continue;
                    }

                    $hasAnswer = true;

                    if (! empty($question['value_to_label'])) {
                        $cleanValue = $question['value_to_label'][$cleanValue] ?? $cleanValue;
                    }

                    if (! isset($question['counts'][$cleanValue])) {
                        $question['counts'][$cleanValue] = 0;
                    }

                    $question['counts'][$cleanValue]++;
                }

                if ($hasAnswer) {
                    $question['answered']++;
                }
            }
            unset($question);
        }

        $preparedQuestions = [];
        foreach ($questions as $question) {
            arsort($question['counts']);

            $labels = array_keys($question['counts']);
            $data = array_values($question['counts']);

            if (count($labels) > 10) {
                $labels = array_slice($labels, 0, 9);
                $dataTop = array_slice($data, 0, 9);
                $otherCount = array_sum(array_slice($data, 9));
                if ($otherCount > 0) {
                    $labels[] = __('attendance.dashboard_other');
                    $dataTop[] = $otherCount;
                }
                $data = $dataTop;
            }

            $completionRate = $eligibleCastellersCount > 0
                ? round(($question['answered'] / $eligibleCastellersCount) * 100, 1)
                : 0;

            $responsesCountForQuestion = array_sum($data);
            $optionRows = [];
            foreach ($labels as $index => $label) {
                $count = $data[$index] ?? 0;
                $optionRows[] = [
                    'label' => $label,
                    'count' => $count,
                    'percentage' => $responsesCountForQuestion > 0
                        ? round(($count / $responsesCountForQuestion) * 100, 1)
                        : 0,
                ];
            }

            $preparedQuestions[] = [
                'name' => $question['name'],
                'label' => $question['label'],
                'type' => $question['type'],
                'labels' => $labels,
                'data' => $data,
                'option_rows' => $optionRows,
                'answered' => $question['answered'],
                'total' => $eligibleCastellersCount,
                'completion_rate' => $completionRate,
            ];
        }

        return [
            'totals' => [
                'registered_attendances' => $attendances->count(),
                'responses' => $respondents,
                'questions' => count($preparedQuestions),
            ],
            'questions' => $preparedQuestions,
            'has_schema' => ! empty($preparedQuestions),
        ];
    }

    public function listAttendersCsv(Request $request, Event $event)
    {
        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }
        $this->authorize('getEvent', $event);
        $colla = Colla::getCurrent();

        $castellers = Casteller::filter($colla)
            ->withStatus(CastellersStatusEnum::ActiveAll())
            ->eloquentBuilder()->get();

        // Build dynamic form field columns from the event's form_schema
        $formFields = [];
        $schemaDict = [];
        if ($event->form_schema && is_array($event->form_schema)) {
            foreach ($event->form_schema as $field) {
                if (isset($field['name']) && isset($field['label'])) {
                    $formFields[] = $field['name'];
                    $schemaDict[$field['name']] = $field['label'];
                }
            }
        }

        $csvHeaders = [
            trans('casteller.alias'),
            trans('casteller.name'),
            trans('casteller.last_name'),
            trans('attendance.attendance_status'),
            trans('attendance.attendance_status_verified'),
            trans('attendance.companions'),
            trans('attendance.tags'),
        ];
        // Add one column per form field
        foreach ($formFields as $fieldName) {
            $csvHeaders[] = $schemaDict[$fieldName];
        }

        $csv = new CsvExporter($csvHeaders);

        foreach ($castellers as $casteller) {
            $attendance = Attendance::getAttendanceCastellerEvent($casteller->getId(), $event->getId());
            if ($attendance != null) {
                $row = [
                    $casteller->getAlias(),
                    $casteller->getName(),
                    $casteller->getLastName(),
                    AttendanceStatus::getById($attendance->status),
                    AttendanceStatus::getById($attendance->status_verified),
                    $attendance->getCompanions(),
                    implode(',', $casteller->tagsArray()),
                ];

                // Add form field values
                $options = $attendance->getOptions() ?: [];
                foreach ($formFields as $fieldName) {
                    $val = $options[$fieldName] ?? '';
                    if (is_array($val)) {
                        $val = implode(', ', $val);
                    }
                    $row[] = $val;
                }

                $csv->addRow($row);
            }
        }

        return $csv->generate();
    }

    /** List Castellers attenders at event for datatable via AJAX*/
    public function postListAttendersAjax(Request $request, Event $event): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $this->authorize('getEvent', $event);

        $answers = [];
        $answersOptions = [];
        foreach ($answers as $answer) {
            $answersOptions[$answer->getId()] = $answer->getName();
        }

        $colla = Colla::getCurrent();

        //params
        $limit = $request->input('length');
        $skip = $request->input('start');

        //order
        $dir = $request->input('order')[0]['dir'];
        $column_order = $request->input('columns')[intval($request->input('order')[0]['column'])]['name'];

        //tags
        $tags = $request->input('tags') ?? [];
        $tags_search_type = $request->input('filter_search_type'); //AND or OR

        $castellersIncluded = $tags;
        $castellersIncludedSearch = in_array($tags_search_type, FilterSearchTypesEnum::validValues()) ? $tags_search_type : FilterSearchTypesEnum::OR;

        $filter = Casteller::filter($colla)
            ->withStatus(CastellersStatusEnum::ActiveAll());

        if ($castellersIncludedSearch == FilterSearchTypesEnum::EXCEPT) {
            $filter->withoutTags($castellersIncluded, FilterSearchTypesEnum::OR);
        } else {
            $filter->withTags($castellersIncluded, $castellersIncludedSearch);
        }

        $castellers =
            $filter
                ->eloquentBuilder()
                ->leftJoin('attendance', function (JoinClause $leftJoin) use ($event) {
                    $leftJoin->on('castellers.id_casteller', '=', 'attendance.casteller_id');
                    $leftJoin->where(function ($query) use ($event) {
                        $query->orWhere('attendance.event_id', $event->getId());
                        $query->orWhereNull('attendance.casteller_id');
                    });
                })
                ->with('attendance', function ($q) use ($event) {
                    $q->where('event_id', $event->getId());
                });

        if ($request->has('status')) {

            $attendanceStatus = $request->input('status');

            $castellers->where(function ($query) use ($attendanceStatus) {
                if (in_array(AttendanceStatus::YES, $attendanceStatus)) {
                    $query->orWhere('attendance.status', AttendanceStatus::YES);
                }

                if (in_array(AttendanceStatus::NO, $attendanceStatus)) {
                    $query->orWhere('attendance.status', AttendanceStatus::NO);
                }

                if (in_array(AttendanceStatus::UNKNOWN, $attendanceStatus)) {

                    $query->orwhere(function ($q) {
                        $q->orWhereNull('attendance.status');
                        $q->orWhere('attendance.status', AttendanceStatus::UNKNOWN);
                    });
                }
            });
        }

        if ($request->has('statusVerified')) {

            $attendanceStatus = $request->input('statusVerified');

            $castellers->where(function ($query) use ($attendanceStatus) {
                if (in_array(AttendanceStatus::YES, $attendanceStatus)) {
                    $query->orWhere('attendance.status_verified', AttendanceStatus::YES);
                }

                if (in_array(AttendanceStatus::NO, $attendanceStatus)) {
                    $query->orWhere('attendance.status_verified', AttendanceStatus::NO);
                }

                if (in_array(AttendanceStatus::UNKNOWN, $attendanceStatus)) {

                    $query->orwhere(function ($q) {
                        $q->orWhereNull('attendance.status_verified');
                        $q->orWhere('attendance.status_verified', AttendanceStatus::UNKNOWN);
                    });
                }
            });
        }

        $data = new \stdClass();
        $data->data = [];

        if ($request->has('search')) {

            $castellers->where(function ($q) use ($request) {
                $q->orWhere('castellers.name', 'LIKE', '%'.$request->input('search')['value'].'%');
                $q->orWhere('castellers.last_name', 'LIKE', '%'.$request->input('search')['value'].'%');
                $q->orWhere('castellers.alias', 'LIKE', '%'.$request->input('search')['value'].'%');
                $q->orWhere('castellers.email', 'LIKE', '%'.$request->input('search')['value'].'%');
                $q->orWhere('castellers.email2', 'LIKE', '%'.$request->input('search')['value'].'%');
            });
        }

        $all_castellers = $castellers;
        $data->recordsTotal = count($all_castellers->get());
        $data->recordsFiltered = count($all_castellers->get());

        $castellers->take($limit)->skip($skip);

        $castellers->orderBy('castellers.'.$column_order, $dir);

        foreach ($castellers->get() as $casteller) {

            $attendance = Attendance::getAttendanceCastellerEvent($casteller->getId(), $event->getId());

            $array_attender = [];

            $array_attender['alias'] = '<a href="'.route('castellers.edit', $casteller->getId()).'" class="btn btn-info" style="margin-right: 6px;"><i class="fa fa-vcard-o"></i></a>';
            $array_attender['alias'] .= $casteller->getDisplayName();
            $array_attender['status'] = Humans::readAttendanceStatus($casteller, $attendance);
            $array_attender['status_verified'] = Humans::readAttendanceStatusVerified($casteller, $attendance);
            $array_attender['attendance_answers'] = Humans::readAttendanceAnswers($casteller, $attendance, $event);
            $array_attender['companions'] = Humans::readAttendanceCompanions($casteller, $attendance, $event);
            $array_attender['last_update'] = Humans::readAttendanceLastUpdate($attendance);

            array_push($data->data, $array_attender);
        }

        return new JsonResponse($data, Response::HTTP_OK);
        //echo json_encode($data);
    }

    /** set attendance status Event / Casteller via AJAX*/
    public function postSetStatusAjax(Request $request): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'id_casteller' => 'required|numeric',
            'id_event' => 'required|numeric',
            'status' => 'required|in:'.implode(',', AttendanceStatus::getStatus()),
        ]);

        if ($validator->fails()) {

            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        $event = Event::query()->find($request->input('id_event'));
        $this->authorize('getEvent', $event);

        $id_casteller = $request->input('id_casteller');
        $id_event = $request->input('id_event');
        $status = $request->input('status');

        Attendance::setStatus($id_casteller, $id_event, $status, 1);

        return new JsonResponse(true, Response::HTTP_OK);
    }

    /** set attendance status verified Event / Casteller via AJAX*/
    public function postSetStatusVerifiedAjax(Request $request): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'id_casteller' => 'required|numeric',
            'id_event' => 'required|numeric',
            'status' => 'required|in:'.implode(',', AttendanceStatus::getStatus()),
        ]);

        if ($validator->fails()) {

            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        $event = Event::query()->find($request->input('id_event'));
        $this->authorize('getEvent', $event);

        $id_casteller = $request->input('id_casteller');
        $id_event = $request->input('id_event');
        $status_verified = $request->input('status');

        Attendance::setStatusVerified($id_casteller, $id_event, $status_verified, 1);

        return new JsonResponse(true, Response::HTTP_OK);
    }

    /** set attendance answers Event / Casteller via AJAX*/
    public function postSetAnswersAjax(Request $request)
    {

        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $event = Event::query()->find($request->input('id_event'));
        $this->authorize('getEvent', $event);

        $id_casteller = $request->input('id_casteller');
        $id_event = $request->input('id_event');

        if (! $request->has('answers')) {

            $answers = [];
        } else {

            $answers = $request->input('answers');
        }

        Attendance::setAnswers($id_casteller, $id_event, $answers, 1);
    }

    /** set attendance answers Event / Casteller via AJAX
     * @throws AuthorizationException
     */
    public function postSetCompanionsAjax(Request $request)
    {

        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $event = Event::query()->find($request->id_event);
        $this->authorize('getEvent', $event);

        $id_casteller = $request->id_casteller;
        $id_event = $request->id_event;
        $companions = $request->companions;

        if ($event->getCompanions()) {
            Attendance::setCompanions($id_casteller, $id_event, $companions, 1);
        }
    }

    /** get list attendance by blocks*/
    public function getListBlocks(Event $event): View
    {
        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $this->authorize('getEvent', $event);
        $colla = Colla::getCurrent();

        $positions = Tag::currentTags(TypeTags::POSITIONS);

        $castellers = [];

        foreach ($positions as $position) {
            $castellersByPosition = Casteller::filter($colla)
                ->withStatus(CastellersStatusEnum::ActivePinya())
                ->withTags([$position->getId()], FilterSearchTypesEnum::OR)
                ->eloquentBuilder()
                ->leftJoin('attendance', function (JoinClause $leftJoin) use ($event) {
                    $leftJoin->on('castellers.id_casteller', '=', 'attendance.casteller_id');
                    $leftJoin->where(function ($query) use ($event) {
                        $query->orWhere('attendance.event_id', $event->getId());
                        $query->orWhereNull('attendance.casteller_id');
                    });
                })
                ->with('attendance', function ($q) use ($event) {
                    $q->where('event_id', $event->getId());
                });

            $castellers[$position->getValue()] = [];

            $statusOK = 0;
            $statusVerifiedOK = 0;

            foreach ($castellersByPosition->get() as $casteller) {

                $castellerInfo = [];

                $attendance = $casteller->attendance->first();

                if (isset($attendance) && $attendance->getStatus() === AttendanceStatus::YES) {
                    $statusOK++;
                }
                if (isset($attendance) && $attendance->getStatusVerified() === AttendanceStatus::YES) {
                    $statusVerifiedOK++;
                }

                $castellerInfo['status'] = Humans::readAttendanceStatus($casteller, $attendance);
                $castellerInfo['status_verified'] = Humans::readAttendanceStatusVerified($casteller, $attendance);
                $castellerInfo['displayName'] = $casteller->getDisplayName();
                $castellers[$position->getValue()][$casteller->getDisplayName()] = $castellerInfo;
            }
            $attendenceStatusOK[$position->getValue()]['statusOK'] = $statusOK;
            $attendenceStatusVerifiedOK[$position->getValue()]['statusVerifiedOK'] = $statusVerifiedOK;

        }

        foreach ($castellers as $position => $castellerList) {
            usort($castellerList, function ($a, $b) {
                return strcmp($a['displayName'], $b['displayName']);
            });
            $newCastellers[$position] = $castellerList;
        }

        $data_content['castellers'] = $newCastellers;
        $data_content['event'] = $event;
        $data_content['positions'] = $positions;
        $data_content['attendanceStatusOK'] = $attendenceStatusOK;
        $data_content['attendenceStatusVerifiedOK'] = $attendenceStatusVerifiedOK;

        return view('events.attendance.list-blocks', $data_content);
    }

    public function notifyMissingAjax(Event $event, Request $request)
    {
        // TODO: Notification message must be > 20 and < 4000 (as defined in telegram)
        $user = $this->user();
        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $custom_message = $request->input('customMessage');
        NotificationService::SendAttendanceReminder($event, customMessage: $custom_message, user: $user);
    }

    public function getVerifyAttendance(Event $event)
    {
        $totpCode = TOTPService::getCurrentCode($event);
        $remainingSeconds = TOTPService::getRemainingSeconds($event);
        $totalSeconds = TOTPService::getPeriod($event);

        return view('events.attendance.verify', compact('event', 'totpCode', 'remainingSeconds', 'totalSeconds'));
    }
}
