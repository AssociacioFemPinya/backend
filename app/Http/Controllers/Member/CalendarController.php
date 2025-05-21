<?php

namespace App\Http\Controllers\Member;

use App\Attendance;
use App\Colla;
use App\Enums\AttendanceStatus;
use App\Enums\EventTypeEnum;
use App\Event;
use App\Helpers\Humans;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class CalendarController extends Controller
{
    /** get member's calendar */
    public function getCalendar(Request $request)
    {

        $event_type = $request->query('event_type') != null ? $request->query('event_type') : EventTypeEnum::Actuacio()->value();
        $event_type_name = trans('config.translation_'.strtolower(EventTypeEnum::getById($event_type)));
        $colla = Colla::getCurrent();

        return view('members.events', compact('event_type', 'event_type_name', 'colla'));
    }

    /** get eventInfo modal via AJAX */
    public function getEventInfoModalAjax(Event $event)
    {
        $data_content['event'] = $event;

        return view('members.modals.get-event-info', $data_content);
    }

    /** get upcoming events via AJAX*/
    public function getEventAttendanceAjax(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_type' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        $casteller = Auth::user()->casteller;
        $colla = Colla::getCurrent();
        $event_type = $request->input('event_type');

        $tagsCasteller = $casteller->tagsArray('id_tag');

        $eventsFilter = Event::filter($colla)->liveOrUpcoming()->visible()->withCastellerTags($tagsCasteller)->withType($event_type);
        $data = $eventsFilter->datatablesFilter($request, ['events.name']);

        $attendances = Attendance::getAttendanceCasteller($casteller->getId());

        foreach ($eventsFilter->eloquentBuilder()->get() as $event) {
            $attendance = $attendances->firstWhere('event_id', $event->getId());
            $urlGoogleCalendar = $event->getUrlGoogleCalendar();

            $array_event = [
                'id' => $event->getId(),
                'name' => $event->name,
                'type' => $event->getTypeName(),
                'start_date' => Humans::parseDate($event->getStartDate(), true),
                'tags' => Humans::readSelectableAttendanceAnswers($event, $attendance),
                'companions' => Humans::readAttendanceCompanions($casteller, $attendance, $event),
                'buttons' => Humans::readEventAttendanceStatus($event, $attendance).'<br><br>
                <button class="btn btn-primary btn-info" data-event_id="'.$event->getId().'" style="opacity: 1;"><i class="fa fa-info"></i></button>
                <br><br>
                <button class="btn btn-primary btn-google-calendar" data-event_id="'.$event->getId().'" data-url_google_calendar="'.$event->getUrlGoogleCalendar().'" style="opacity: 1;">
                    <i class="fa fa-calendar"></i>
                </button>',
                'isOpen' => $event->isOpen(),
                'dropDownButton' => (count($event->getAttendanceAnswersOptions()) > 0 | $event->getCompanions()) ? '<i class="fa-solid fa-question-circle" style="font-size: 23px;"></i>' : '',
            ];
            $data->data[] = $array_event;
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /** set attendance status Event  via AJAX*/
    public function setEventAttendanceStatusAjax(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_event' => 'required|numeric',
            'status' => 'required|in:'.implode(',', AttendanceStatus::getStatus()),
        ]);
        if ($validator->fails()) {
            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        $casteller = Auth::user()->casteller;
        $id_event = $request->input('id_event');
        $status = $request->input('status');

        $event = Event::find($id_event);
        if (! $event || $event->getCollaId() != $casteller->getCollaId() || ! $event->isOpen()) {
            abort(404);
        }

        Attendance::setStatus($casteller->getId(), $id_event, $status, 1);

        return new JsonResponse(true, Response::HTTP_OK);
    }

    /** set attendance answers Event / Casteller via AJAX*/
    public function postSetAnswersAjax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_event' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        $casteller = Auth::user()->casteller;
        $id_event = $request->input('id_event');
        $event = Event::find($id_event);
        if (! $event || $event->getCollaId() != $casteller->getCollaId() || ! $event->isOpen()) {
            abort(404);
        }

        $answers = ! $request->has('answers') ? [] : $request->input('answers');
        Attendance::setAnswers($casteller->getId(), $id_event, $answers, 1);

        return new JsonResponse(true, Response::HTTP_OK);
    }

    /** set attendance answers Event / Casteller via AJAX*/
    public function postSetCompanionsAjax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_event' => 'required|numeric',
            'companions' => 'required|numeric|min:0|max:6',
        ]);
        if ($validator->fails()) {
            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        $casteller = Auth::user()->casteller;
        $id_event = $request->input('id_event');
        $event = Event::find($id_event);
        if (! $event || $event->getCollaId() != $casteller->getCollaId() || ! $event->isOpen()) {
            abort(404);
        }

        $companions = ! $request->has('companions') ? [] : $request->input('companions');
        Attendance::setCompanions($casteller->getId(), $id_event, $companions, 1);

        return new JsonResponse(true, Response::HTTP_OK);
    }
}
