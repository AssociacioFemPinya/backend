<?php

namespace App\Http\Controllers;

use App\BoardEvent;
use App\Colla;
use App\Event;
use App\Managers\RondesManager;
use App\Ronda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\ParameterBag;

class EventRondesController extends Controller
{
    /** List Castellers attenders at event*/
    public function getList(Event $event): View
    {

        $user = $this->user();

        if (! $user->can('view boards') && ! $user->can('edit boards')) {
            abort(404);
        }

        $this->authorize('getEvent', $event);

        $colla = Colla::getCurrent();

        $boardsInRondes = $event->rondes()->pluck('board_event_id')->toArray();
        $boardEventsNotInRondes = BoardEvent::filter($colla)->inEvent($event)->excludeBoardEvents($boardsInRondes)->eloquentBuilder()->get();

        $data_content['event'] = $event;
        $data_content['rondes'] = $event->getRondes();
        $data_content['pinyes'] = $boardEventsNotInRondes;

        return view('events.rondes.list', $data_content);
    }

    public function postListAjax(Request $request, Event $event): JsonResponse
    {
        $user = $this->user();
        if (! $user->can('view boards') && ! $user->can('edit boards')) {
            abort(404);
        }
        $this->authorize('getEvent', $event);

        $rondes = $event->getRondes();

        $data = new \stdClass();
        $data->data = [];

        foreach ($rondes as $ronda) {
            $array_ronda = [];

            $array_ronda['hamb'] = '';
            $array_ronda['ronda'] = $ronda->getRonda();
            $array_ronda['name'] = $ronda->getName();
            $array_ronda['board_event'] = $ronda->getBoardEvent()->getBoard()->getName();

            $data->data[] = $array_ronda;
        }

        return new JsonResponse($data, Response::HTTP_OK);

    }

    public function postAddRondaAjax(RondesManager $manager, Request $request, Event $event): JsonResponse
    {
        $user = $this->user();

        if (! $user->can('view boards') && ! $user->can('edit boards')) {
            return new JsonResponse(Response::HTTP_FORBIDDEN);
        }

        $this->authorize('getEvent', $event);

        if ($event->getCollaId() !== Colla::getCurrent()->getId()) {
            return new JsonResponse(Response::HTTP_FORBIDDEN);
        }

        $newRonda = (! is_null($event->getLastRonda())) ? $event->getLastRonda()->getRonda() + 1 : 1;
        $bag = new ParameterBag([
            'ronda' => $newRonda,
        ]);

        $pinya = '';
        $template = '';
        $buttonDelete = '';
        $ronda = '';

        $boardEventId = ($request->input('id_pinya')) ? intval($request->input('id_pinya')) : null;
        if ($boardEventId && $boardEvent = BoardEvent::query()->where('event_id', $event->getId())->find($boardEventId)) {
            $ronda = $manager->createRonda($bag, $event, $boardEvent);
            $pinya = $boardEvent->getDisplayName();
            $template = $boardEvent->getBoard()->getName();
            $buttons = '<button type="button" class="btn btn-sm btn-warning btn-edit-pinya mr-5" data-id_pinya="'.$boardEvent->getId().'" data-toggle="tooltip" data-placement="bottom" title="'.trans('boards.tooltip_edit').'"><i class="fa fa-pencil"></i></button> ';
            $buttons .= '<a href="'.route('event.board', ['event' => $boardEvent->getEvent()->getId(), 'boardEvent' => $boardEvent->getId()]).'" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="bottom" title="'.trans('rondes.tooltip_editor').'"><img src="'.asset('media/img/ico_pinya_o3.svg').'" style="width: 15px;" alt=""></a> ';
            $buttons .= '<button type="button" class="btn btn-circle btn-delete-ronda btn-dual-secondary mr-5" data-id_ronda="'.$ronda->getId().'" data-toggle="tooltip" data-placement="bottom" title="'.trans('rondes.tooltip_remove_ronda').'"><i class="fa fa-arrow-right"></i></button> ';

        }

        return new JsonResponse(['ronda' => $newRonda, 'pinya' => $pinya, 'template' => $template, 'buttons' => $buttons, 'idPinya' => $ronda->getId()], Response::HTTP_OK);
    }

    public function postDestroyRondaAjax(RondesManager $manager, Ronda $ronda): JsonResponse
    {
        $user = $this->user();
        if (! $user->can('edit boards')) {
            return new JsonResponse(Response::HTTP_FORBIDDEN);
        }

        if ($ronda->getEvent()->getCollaId() !== Colla::getCurrent()->getId()) {
            return new JsonResponse(Response::HTTP_FORBIDDEN);
        }
        $boardEvent = $ronda->getBoardEvent();
        $displayName = $boardEvent->getName();
        $template = $boardEvent->getBoard()->getName();
        $buttons = '<button class="btn btn-circle btn-dual-secondary mr-5" id="addRonda" data-id_pinya="'.$boardEvent->getId().'" data-toggle="tooltip" data-placement="bottom" title="'.trans('rondes.tooltip_add_ronda').'"><i class="fa fa-arrow-left"></i></button> ';
        $buttons .= '<button class="btn btn-sm btn-warning btn-edit-pinya mr-5" data-id_pinya="'.$boardEvent->getId().'" data-toggle="tooltip" data-placement="bottom" title="'.trans('boards.tooltip_edit').'"><i class="fa fa-pencil"></i></button> ';
        $buttons .= '<a href="'.route('event.board', ['event' => $boardEvent->getEvent()->getId(), 'boardEvent' => $boardEvent->getId()]).'" class="btn btn-sm btn-primary mr-5" data-toggle="tooltip" data-placement="bottom" title="'.trans('rondes.tooltip_editor').'"><img src="'.asset('media/img/ico_pinya_o3.svg').'" style="width: 15px;" alt=""></a> ';

        $manager->deleteRonda($ronda);

        return new JsonResponse(['displayName' => $displayName, 'template' => $template, 'buttons' => $buttons], Response::HTTP_OK);
    }

    public function postUpdateRondaAjax(RondesManager $manager, Request $request, Ronda $ronda): JsonResponse
    {

        $user = $this->user();
        if (! $user->can('edit boards')) {
            return new JsonResponse(Response::HTTP_FORBIDDEN);
        }

        if ($ronda->getEvent()->getCollaId() !== Colla::getCurrent()->getId()) {
            return new JsonResponse(Response::HTTP_FORBIDDEN);
        }

        $rondaNum = ($request->input('ronda_num')) ? intval($request->input('ronda_num')) : null;

        $bag = new ParameterBag([
            'ronda' => $rondaNum,
        ]);

        $manager->updateRonda($ronda, $bag);

        return new JsonResponse(Response::HTTP_OK);
    }
}
