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

        $csv = new CsvExporter([
            trans('casteller.alias'),
            trans('casteller.name'),
            trans('casteller.last_name'),
            trans('attendance.attendance_status'),
            trans('attendance.attendance_status_verified'),
            trans('attendance.attendance_answers'),
            trans('attendance.companions'),
            trans('attendance.tags')]
        );

        foreach ($castellers as $casteller) {
            $attendance = Attendance::getAttendanceCastellerEvent($casteller->getId(), $event->getId());
            if ($attendance != null) {
                $answersOptions = [];
                if ($attendance->getOptions() != null) {
                    foreach ($attendance->getOptions() as $option) {
                        array_push($answersOptions, Tag::find($option)->getName());
                    }
                }

                $csv->addRow([
                    $casteller->getAlias(),
                    $casteller->getName(),
                    $casteller->getLastName(),
                    AttendanceStatus::getById($attendance->status),
                    AttendanceStatus::getById($attendance->status_verified),
                    implode(',', $answersOptions),
                    $attendance->getCompanions(),
                    implode(',', $casteller->tagsArray()),
                ]);
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

        $answers = $event->getAttendanceAnswers();
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

        $castellers = Casteller::filter($colla)
            ->withStatus(CastellersStatusEnum::ActiveAll())
            ->withTags($castellersIncluded, $castellersIncludedSearch)
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
            $array_attender['attendance_answers'] = Humans::readAttendanceAnswers($casteller, $answersOptions, $attendance);
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
