<?php

namespace App\Http\Controllers;

use App\Board;
use App\BoardEvent;
use App\BoardPosition;
use App\Casteller;
use App\Colla;
use App\Enums\AttendanceStatus;
use App\Enums\BasesEnum;
use App\Enums\CastellersStatusEnum;
use App\Enums\ScaledAttendanceStatus;
use App\Enums\TypeTags;
use App\Event;
use App\Helpers\RenderHelper;
use App\Managers\AttendanceManager;
use App\Managers\EventBoardManager;
use App\Row;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

final class EventBoardController extends Controller
{
    /** get board form an event*/
    public function getBoard(Event $event, ?BoardEvent $boardEvent = null): View
    {
        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }
        $this->authorize('getEvent', $event);

        $colla = Colla::getCurrent();

        if ($boardEvent) {
            $board = $boardEvent->getBoard();
        } else {

            $board = $event->getBoards()->last();
            if (! $board) {

                $data_content['tags'] = $colla->getTags(TypeTags::EVENTS);
                $data_content['boardsColla'] = $colla->getBoards();

                Session::flash('status_ko', trans('event.add_first_board_txt'));

                return view('events.list', $data_content);
            }

            $boardEvent = BoardEvent::filter($colla)
                ->inEvent($event)
                ->usingBoard($board)
                ->get()
                ->first();
        }

        $boardsInEvent = $event->getBoardsEvent();
        $boardsInEvent = $boardsInEvent->sort(function ($a, $b) {
            return strcasecmp($a->getDisplayName(), $b->getDisplayName());
        });

        $boardFavourites = BoardEvent::filter($colla)->favouritesByBoard($board)->excludeBoardEvents($boardEvent)->orderByEventDate()->orderByBoardEventName()->eloquentBuilder()->get();
        $boardNotFavourites = BoardEvent::filter($colla)->notFavouritesByBoard($board)->excludeBoardEvents($boardEvent)->orderByEventDate()->orderByBoardEventName()->eloquentBuilder()->get();

        $positions = $colla->getTags(TypeTags::POSITIONS); //Tag::currentTags(TypeTags::POSITIONS, $colla, true

        $data_content['colla'] = $colla;
        $data_content['event'] = $event;
        $data_content['positions'] = $positions;
        $data_content['boardsColla'] = $colla->getBoards();
        $data_content['board'] = $board;
        $data_content['boardEvent'] = $boardEvent;
        $data_content['boardEventId'] = $boardEvent->getId();
        $data_content['boardsInEvent'] = $boardsInEvent;
        $data_content['boardFavourites'] = $boardFavourites;
        $data_content['boardNotFavourites'] = $boardNotFavourites;

        return view('events.boards.home', $data_content);
    }

    public function postLoadPositionsAjax(EventBoardManager $eventBoardManager, Request $request, BoardEvent $boardEvent): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('edit events')) {

            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'positionId' => 'nullable|numeric',
            'attendanceStatus' => 'nullable|array',
            'attendanceStatusVerified' => 'nullable|array',
            'filterText' => 'nullable|alpha_num',
            'heightType' => 'string',
        ]);

        if ($validator->fails()) {

            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        $event = $boardEvent->getEvent();

        $this->authorize('getEvent', $event);
        $colla = Colla::getCurrent();
        $positionId = $request->input('positionId') ? intval($request->input('positionId')) : null;
        $position = $request->input('positionId') ? $colla->tags()->find($positionId) : ['name' => trans('boards.search')];
        $attendanceStatus = $request->input('attendanceStatus') ?? [];
        $attendanceStatusVerified = $request->input('attendanceStatusVerified') ?? [];
        $filterText = $request->input('filterText') ?? null;
        $heightType = $request->input('heightType');

        $castellers = $eventBoardManager->listCastellersPositionFiltered($colla, $boardEvent, $positionId, $attendanceStatus, $attendanceStatusVerified, $filterText, CastellersStatusEnum::ActivePinya());
        $castellers = $castellers->sort(function ($a, $b) {
            return strcasecmp($a->getDisplayName(), $b->getDisplayName());
        })->values();

        $castellersNotPositioned = $castellers->filter(function ($casteller) {
            return $casteller->boardPosition->isEmpty();
        })->values();

        $castellersPositioned = $castellers->filter(function ($casteller) {
            return $casteller->boardPosition->isNotEmpty();
        })->values();

        $result = '';

        foreach ($castellersNotPositioned as $casteller) {

            $result .= '<div class="col-6 col-sm-4 col-md-12 col-lg-6 col-xl-4 p-5">';

            $tooltipTxt = $casteller->getFullName() ?? $casteller->getAlias();
            if ($heightType === 'height') {
                $tooltipTxt .= '<br>'.$casteller->getRelativeHeight().'cm';
            } else {
                $tooltipTxt .= '<br>'.$casteller->getRelativeShoulderHeight().'cm';
            }

            if ($casteller->attendance->isNotEmpty()) {

                $status = $casteller->attendance->first() ? $casteller->attendance->first()->getScaledAttendance() : ScaledAttendanceStatus::UNKNOWN();
                $result .= RenderHelper::renderCastellerButton($casteller, $status, $tooltipTxt);

            } else {
                $result .= RenderHelper::renderCastellerButton($casteller, ScaledAttendanceStatus::UNKNOWN(), $tooltipTxt);
            }

            $result .= '</div>';
        }

        foreach ($castellersPositioned as $casteller) {

            $result .= '<div class="col-6 col-sm-4 col-md-12 col-lg-6 col-xl-4 p-5">';

            $tooltipTxt = $casteller->getFullName() ?? $casteller->getAlias();
            if ($heightType === 'height') {
                $tooltipTxt .= '<br>'.$casteller->getRelativeHeight().'cm';
            } else {
                $tooltipTxt .= '<br>'.$casteller->getRelativeShoulderHeight().'cm';
            }
            $status = $casteller->attendance->first() ? $casteller->attendance->first()->getScaledAttendance() : ScaledAttendanceStatus::UNKNOWN();
            $result .= RenderHelper::renderCastellerButton($casteller, $status, $tooltipTxt, true);

            $result .= '</div>';
        }

        return new JsonResponse(['rows' => $result, 'position' => $position], Response::HTTP_OK);
    }

    public function postPutCastellerAjax(EventBoardManager $eventBoardManager, Request $request, int $eventBoardId): JsonResponse
    {
        $user = $this->user();

        if (! $user->can('edit events')) {

            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'castellerId' => 'required|numeric',
            'rowId' => 'required|numeric',
            'eventId' => 'required|numeric',
            'base' => 'required|in:'.implode(',', BasesEnum::getTypes()),
        ]);

        if ($validator->fails()) {

            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        $castellerId = $request->input('castellerId');
        $rowId = $request->input('rowId');
        $eventId = $request->input('eventId');
        $base = $request->input('base');

        $colla = Colla::getCurrent();

        /** @var Event $event */
        $event = $colla->events->find($eventId);

        /** @var Casteller $casteller */
        $casteller = $colla->castellers->find($castellerId);

        if (! $event || ! $casteller) {

            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        /** @var BoardEvent $boardEvent */
        $boardEvent = BoardEvent::query()
            ->where('event_id', $event->getId())
            ->find($eventBoardId);

        if (! $boardEvent) {

            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        $board = $boardEvent->getBoard();

        /** @var Row $row */
        $row = $board->rows()
            ->where('div_id', $rowId)
            ->where('base', $base)
            ->first();

        if (! $row) {

            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        $boardPosition = $eventBoardManager->putCastellerOnBoard($boardEvent, $casteller, $row);

        if ($boardPosition) {

            return new JsonResponse(
                ['castellerName' => $casteller->getDisplayName(), 'divId' => $boardPosition->getRow()->getDivId(), 'castellerHeight' => $casteller->getRelativeHeight(), 'castellerAttendance' => ($casteller->getEventAttendance($eventId)) ? $casteller->getEventAttendance($eventId)->getStatus() : '??', 'castellerVerifiedAttendance' => ($casteller->getEventAttendance($eventId)) ? $casteller->getEventAttendance($eventId)->getStatusVerified() : '??', 'castellerShoulderHeight' => $casteller->getRelativeShoulderHeight(), 'castellerActivePinya' => $casteller->isActivePinya()],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(false, Response::HTTP_CONFLICT);
    }

    public function postSwapCastellersAjax(EventBoardManager $eventBoardManager, Request $request, int $eventBoardId): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('edit events')) {

            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'rowId' => 'required|numeric',
            'eventId' => 'required|numeric',
            'base' => 'required|in:'.implode(',', BasesEnum::getTypes()),
            'rowSwapId' => 'required|numeric',
        ]);

        if ($validator->fails()) {

            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        $rowId = $request->input('rowId');
        $eventId = $request->input('eventId');
        $rowSwapId = $request->input('rowSwapId');
        $base = $request->input('base');

        $colla = Colla::getCurrent();

        /** @var Event $event */
        $event = $colla->events()->find($eventId);

        /** @var BoardEvent $boardEvent */
        $boardEvent = BoardEvent::query()->where('event_id', $event->getId())->find($eventBoardId);

        if (! $boardEvent) {

            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        $board = $boardEvent->getBoard();

        /** @var Row $row */
        $row = $board->rows()
            ->where('div_id', $rowId)
            ->where('base', $base)
            ->first();
        /** @var Row $rowSwap */
        $rowSwap = $board->rows()
            ->where('div_id', $rowSwapId)
            ->where('base', $base)
            ->first();

        if (! $row || ! $rowSwap) {

            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        $arrayBoarPositionsSwapped = $eventBoardManager->swapCastellersOnBoard($boardEvent, $row, $rowSwap);

        if (! $arrayBoarPositionsSwapped) {

            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        $castellerSwapped1 = $arrayBoarPositionsSwapped[0]->getCasteller();

        if (! $arrayBoarPositionsSwapped[1]) {
            return new JsonResponse(
                [
                    'castellerName' => '',
                    'divId' => $row->getDivId(),
                    'castellerHeight' => '',
                    'castellerShoulderHeight' => '',

                    'castellerSwappedName' => $castellerSwapped1->getDisplayName(),
                    'divSwappedId' => $arrayBoarPositionsSwapped[0]->getRow()->getDivId(),
                    'castellerSwappedHeight' => $castellerSwapped1->getRelativeHeight(),
                    'castellerSwappedShoulderHeight' => $castellerSwapped1->getRelativeShoulderHeight(),
                    'castellerSwappedAttendance' => ($castellerSwapped1->getEventAttendance($eventId)) ? $castellerSwapped1->getEventAttendance($eventId)->getStatus() : '??',
                    'castellerSwappedVerifiedAttendance' => ($castellerSwapped1->getEventAttendance($eventId)) ? $castellerSwapped1->getEventAttendance($eventId)->getStatusVerified() : '??',
                    'castellerSwappedActivePinya' => $castellerSwapped1->isActivePinya(),

                ],

                Response::HTTP_OK
            );
        }

        $castellerSwapped2 = $arrayBoarPositionsSwapped[1]->getCasteller();

        return new JsonResponse(
            [
                'castellerName' => $castellerSwapped1->getDisplayName(),
                'divId' => $arrayBoarPositionsSwapped[0]->getRow()->getDivId(),
                'castellerHeight' => $castellerSwapped1->getRelativeHeight(),
                'castellerShoulderHeight' => $castellerSwapped1->getRelativeShoulderHeight(),
                'castellerAttendance' => ($castellerSwapped1->getEventAttendance($eventId)) ? $castellerSwapped1->getEventAttendance($eventId)->getStatus() : '??',
                'castellerVerifiedAttendance' => ($castellerSwapped1->getEventAttendance($eventId)) ? $castellerSwapped1->getEventAttendance($eventId)->getStatusVerified() : '??',
                'castellerActivePinya' => $castellerSwapped1->isActivePinya(),

                'castellerSwappedName' => $castellerSwapped2->getDisplayName(),
                'divSwappedId' => $arrayBoarPositionsSwapped[1]->getRow()->getDivId(),
                'castellerSwappedHeight' => $castellerSwapped2->getRelativeHeight(),
                'castellerSwappedShoulderHeight' => $castellerSwapped2->getRelativeShoulderHeight(),
                'castellerSwappedAttendance' => ($castellerSwapped2->getEventAttendance($eventId)) ? $castellerSwapped2->getEventAttendance($eventId)->getStatus() : '??',
                'castellerSwappedVerifiedAttendance' => ($castellerSwapped2->getEventAttendance($eventId)) ? $castellerSwapped2->getEventAttendance($eventId)->getStatusVerified() : '??',
                'castellerSwappedActivePinya' => $castellerSwapped2->isActivePinya(),

            ],
            Response::HTTP_OK
        );
    }

    public function getLoadMapAjax(EventBoardManager $manager, BoardEvent $boardEvent, string $base): JsonResponse
    {
        $user = $this->user();

        if (! $user->can('edit events')) {

            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        $colla = Colla::getCurrent();
        $event = $boardEvent->getEvent();

        if ($event->getCollaId() !== $colla->getId()) {

            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        $map_board = $manager->loadMapByBoardEventId($colla, $boardEvent, $base);
        $castellers = $manager->listCastellersPositionFiltered($colla, $boardEvent);

        $map_board->map(function ($item) use ($castellers) {
            $casteller = $castellers->where('id_casteller', $item->casteller->id_casteller)->first();
            if ($casteller->attendance->isNotEmpty()) {
                $item->casteller->castellerAttendance = $casteller->attendance[0]->getStatus();
            }
            if ($casteller->attendance->isNotEmpty()) {
                $item->casteller->castellerVerifiedAttendance = $casteller->attendance[0]->getStatusVerified();
            }
            $item->casteller->activePinya = $casteller->isActivePinya();
            $item->casteller->height = $casteller->getRelativeHeight();
            $item->casteller->shoulderHeight = $casteller->getRelativeShoulderHeight();

            return $item;
        });

        return new JsonResponse($map_board, Response::HTTP_OK);
    }

    public function getLoadBase(BoardEvent $boardEvent, string $base): JsonResponse
    {
        $user = $this->user();

        $colla = Colla::getCurrent();
        $event = $boardEvent->getEvent();

        if ($event->getCollaId() !== $colla->getId()) {

            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($boardEvent->getBoard()->getHtmlBase($base), Response::HTTP_OK);
    }

    public function postAttachBoard(Request $request, Event $event): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }
        $this->authorize('getEvent', $event);

        $request->validate([
            'board' => 'required|numeric',
            'name' => 'nullable|string',
        ]);

        $attributes = ($request->input('name')) ? ['name' => $request->input('name')] : [];

        $event->boards()->attach($request->input('board'), $attributes);

        return redirect()->route('event.board', $event->getId());
    }

    public function postImportBoardEvent(EventBoardManager $manager, Request $request, BoardEvent $boardEvent): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        $event = $boardEvent->getEvent();

        $request->validate(['importBoardEvent' => 'required|numeric']);

        $importBoardEventId = $request->input('importBoardEvent');

        $importBoardEvent = BoardEvent::query()->find($importBoardEventId);

        if (! $importBoardEvent || $importBoardEvent->getEvent()->getColla() != $colla) {
            Session::flash('status_ko', trans('event.eventboard_import_colla_different'));

            return redirect()->route('event.board', $event->getId(), $boardEvent->getId());
        }

        if ($importBoardEvent->getBoard() != $boardEvent->getBoard()) {
            Session::flash('status_ko', trans('event.eventboard_import_board_different'));

            return redirect()->route('event.board', $event->getId(), $boardEvent->getId());
        }

        $manager->deletePositionsByBoardEventId($boardEvent->getId());
        $manager->importPositionsByBoardEventId($importBoardEventId, $boardEvent);

        Session::flash('status_ok', trans('event.eventboard_import_success'));

        return redirect()->route('event.board', [$event->getId(), $boardEvent->getId()]);
    }

    public function getCastellerInfoAjax(AttendanceManager $attendanceManager, BoardEvent $boardEvent, int $divId, string $base): JsonResponse
    {
        $user = $this->user();

        if (! $user->can('edit events')) {

            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        if (! in_array($base, array_values(BasesEnum::getTypes()))) {

            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        $colla = Colla::getCurrent();

        /** @var Row $row */
        $row = $colla->rows
            ->where('div_id', $divId)
            ->where('base', $base)
            ->where('board_id', $boardEvent->getBoardId())
            ->first();

        if (! $row) {

            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        /** @var BoardPosition $boardPosition */
        $boardPosition = $boardEvent->boardPosition()
            ->where('colla_id', $colla->getId())
            ->where('row_id', $row->getId())
            ->first();

        if (! $boardPosition) {

            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        $casteller = $boardPosition->getCasteller();

        if (! $casteller) {

            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        $attendance = $attendanceManager->fetchAttendanceCastellerEvent($casteller->getId(), $boardEvent->getEventId());

        return new JsonResponse(
            [
                'castellerName' => $casteller->getFullName(),
                'castellerAlias' => $casteller->getAlias(),
                'castellerPhoto' => $casteller->getProfileImage('xs'),
                'castellerHeight' => trans('casteller.relative_height').': '.$casteller->getRelativeHeight().'cm',
                'castellerShoulderHeight' => trans('casteller.relative_shoulder_height').': '.$casteller->getRelativeShoulderHeight().'cm',
                'castellerStatus' => trans('attendance.attendance_status').': <i class="'.(RenderHelper::getAttendanceIcon($attendance?->getStatus())).'"></i>',
                'castellerStatusVerified' => trans('attendance.attendance_status_verified').': <i class="'.(RenderHelper::getAttendanceIcon($attendance?->getStatusVerified())).'"></i>',
            ],
            Response::HTTP_OK);
    }

    public function postAjaxEmptyRow(EventBoardManager $eventBoardManager, Request $request, BoardEvent $boardEvent): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('edit events')) {

            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'divId' => 'required|numeric',
            'eventId' => 'required|numeric',
            'base' => 'required|in:'.implode(',', BasesEnum::getTypes()),
        ]);

        if ($validator->fails()) {

            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        $colla = Colla::getCurrent();

        /** @var Event $event */
        if (! $event = $colla->events()->find($request->input('eventId'))) {

            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        if ($boardEvent->getEventId() !== $event->getId()) {

            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        if ($eventBoardManager->emptyRowFromEventBoard($boardEvent, (int) $request->input('divId'), $request->input('base'))) {

            return new JsonResponse(true, Response::HTTP_OK);
        }

        return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
    }

    public function postToDisplay(EventBoardManager $manager, Request $request): JsonResponse
    {
        $user = $this->user();

        if (! $user->can('edit events')) {

            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {

            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(
            $manager->eventBoardToggleToDisplay((int) $request->input('id'),
                $user->getCollaId()), Response::HTTP_OK
        );
    }

    public function postAddFavourite(EventBoardManager $manager, Request $request): JsonResponse
    {
        $user = $this->user();

        if (! $user->can('edit events')) {

            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {

            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(
            $manager->eventBoardToggleAddFavourite((int) $request->input('id'),
                $user->getCollaId()), Response::HTTP_OK
        );
    }

    public function postEditBoardEventAjax(EventBoardManager $manager, Request $request, BoardEvent $boardEvent): RedirectResponse
    {
        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        $event = $boardEvent->getEvent();

        if ($boardEvent->getEvent()->getCollaId() !== $colla->getId()) {
            Session::flash('status_ko', trans('event.eventboard_import_colla_different'));

            return redirect()->route('event.board', $event->getId());
        }

        $request->validate([
            'name' => 'nullable|string',
            'fromRondes' => 'nullable|string',
        ]);

        $manager->eventBoardSetName($boardEvent, ($request->input('name')) ?: '');

        if ($request->input('fromRondes') == 1) {
            return redirect()->route('event.rondes', $event->getId());
        } else {
            return redirect()->route('event.board', [$event->getId(), $boardEvent->getId()]);
        }
    }

    public function postEmptyBoardAjax(EventBoardManager $manager, Request $request, BoardEvent $boardEvent): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('edit events')) {
            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        $colla = Colla::getCurrent();

        $event = $boardEvent->getEvent();

        if ($event->getCollaId() !== $colla->getId()) {
            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        $manager->deletePositionsByBoardEventId($boardEvent->getId());

        return new JsonResponse(true, Response::HTTP_OK);

    }

    public function postRemoveMissingAjax(EventBoardManager $manager, Request $request, BoardEvent $boardEvent): JsonResponse
    {
        $user = $this->user();

        if (! $user->can('edit events')) {
            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        $colla = Colla::getCurrent();
        $event = $boardEvent->getEvent();

        if ($event->getCollaId() !== $colla->getId()) {
            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'attendanceType' => 'required|in:status,status_verified',
            'attendanceStatus' => 'required|in:onlyNo,allButYes',
        ]);

        if ($validator->fails()) {

            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        $attendanceType = $request->input('attendanceType');

        if ($request->input('attendanceStatus') == 'onlyNo') {
            $castellersToRemove = Casteller::filter($colla)->withEventAttendance($event, [attendanceStatus::NO], $attendanceType)->getCastellerIds();
        } else {
            $castellersToRemove = array_merge(
                Casteller::filter($colla)->withEventAttendance($event, [attendanceStatus::NO, attendanceStatus::UNKNOWN], $attendanceType)->getCastellerIds(),
                Casteller::filter($colla)->withMissingAttendance($event, $attendanceType)->getCastellerIds(),
            );
        }

        $positionsToRemove =
        BoardPosition::filter($colla)
            ->fromCastellers($castellersToRemove)
            ->inBoardEvent($boardEvent)
            ->getRowInfo()
            ->toArray();

        if ($castellersToRemove) {
            $manager->deletePositionsByBoardEventId($boardEvent->getId(), $castellersToRemove);
        }

        return new JsonResponse($positionsToRemove, Response::HTTP_OK);

    }

    public function getEditBoardEventModalAjax(boardEvent $boardEvent, int $fromRondes = 0): View
    {

        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        if ($boardEvent->getEvent()->getCollaId() !== Colla::getCurrent()->getId()) {
            abort(404);
        }

        $data_content['boardEvent'] = $boardEvent;
        $data_content['fromRondes'] = $fromRondes;

        return view('events.boards.modals.modal-edit', $data_content);
    }

    public function postDestroyBoardEvent(BoardEvent $boardEvent): RedirectResponse
    {

        $user = $this->user();
        $id_event = $boardEvent->event_id;
        $bev = $boardEvent;

        if (! $user->can('edit events')) {
            abort(404);
        }

        $boardEvent->delete();

        Session::flash('status_ok', trans('event.eventboard_destroyed'));

        return redirect(route('event.board', ['event' => $id_event]));
    }
}
