<?php

namespace App\Http\Controllers;

use App\Colla;
use App\Enums\FilterSearchTypesEnum;
use App\Enums\TypeTags;
use App\Event;
use App\Helpers\Humans;
use App\Managers\EventsManager;
use App\Managers\MultieventManager;
use App\Multievent;
use App\Period;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;

class EventsController extends Controller
{
    /** get list of Events*/
    public function getList(): View
    {

        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        $data_content['periods'] = $colla->periods;
        $data_content['currentPeriod'] = $colla->getCurrentPeriod();
        $data_content['tags'] = $colla->getTags(TypeTags::EVENTS);
        $data_content['boardsColla'] = $colla->getBoards();
        $data_content['tags_event_type'] = Event::getTypes();
        $data_content['multievents'] = $colla->multievents()->orderBy('name')->get();

        return view('events.list', $data_content);
    }

    /** get $time = upcoming/past events via AJAX*/
    public function postListAjax(Request $request, string $time = 'upcoming'): JsonResponse
    {
        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        $collaConfig = $colla->getConfig();

        //tags
        $tags = $request->input('tags', []);
        $tags_search_type = $request->input('filter_search_type');
        $search_period = $request->input('search_period') ?? null;
        $tag_event_type = (int) $request->input('tags_event_type');

        $eventsFilter = Event::filter($colla);

        if ($tags_search_type == FilterSearchTypesEnum::AND) {
            $eventsFilter->withTags($tags, FilterSearchTypesEnum::AND);
        } elseif ($tags_search_type == FilterSearchTypesEnum::OR) {
            $eventsFilter->withTags($tags, FilterSearchTypesEnum::OR);
        }

        if ($time === 'upcoming') {
            $eventsFilter->upcoming();
        } elseif ($time === 'past') {
            $eventsFilter->past();
        }

        if ($search_period !== 0) {
            $period = Period::find($search_period);
        } else {
            $period = $colla->getCurrentPeriod();
        }

        $eventsFilter->withPeriod($period);

        if ($tag_event_type != 0) {
            $eventsFilter->withType($tag_event_type);
        }

        $data = $eventsFilter->datatablesFilter($request, ['events.name']);
        $now = Carbon::now();

        $boardsEnabled = $collaConfig->getBoardsEnabled();

        $events = $eventsFilter->eloquentBuilder()
            ->with('attendances')
            ->with('boardsEvent')
            ->with('tags')
            ->with('colla')
            ->with('multievent')
            ->get();

        /** @var Event $event */
        foreach ($events as $event) {
            $array_event = [];

            $eventId = $event->getId();
            $array_event['id_event'] = $eventId;
            $array_event['id_multievent'] = $event->getMultieventId();

            $namePrefix = '';
            if ($event->belongsToMultievent() && $event->getMultievent()) {
                $multieventId = $event->getMultievent()->getId();
                $multieventName = $event->getMultievent()->getName();

                if ($user->can('edit events')) {
                    $url = route('multievents.edit', $multieventId);
                    $namePrefix = '<a href="'.$url.'" class="multievent-badge" data-toggle="tooltip" title="'.trans('multievent.belongs_to_multievent').$multieventName.'">
                                    <i class="fa fa-link multievent-icon"></i>
                                  </a>';
                } else {
                    $namePrefix = '<span class="multievent-badge" data-toggle="tooltip" title="'.trans('multievent.belongs_to_multievent').$multieventName.'">
                                    <i class="fa fa-link multievent-icon"></i>
                                  </span>';
                }
            }

            $array_event['name'] = $namePrefix.$event->getName().'
                                                    <span class="text-success"><i class="fa-solid fa-check-double"></i>'.$event->countAttenders()['verified_ok'].'</span>
                                                    <span class="text-success"><i class="fa-solid fa-check"></i>'.$event->countAttenders()['ok'].'</span>
                                                    <span class="text-danger"><i class="fa-solid fa-close"></i>'.$event->countAttenders()['nok'].'</span>
                                                    <span class="text-warning"><i class="fa-solid fa-question"></i>'.$event->countAttenders()['unknown'].'</span>';
            $array_event['type'] = $event->getTypeName();
            $array_event['tags'] = Humans::readEventColumn($event, 'tags', 'right');
            $array_event['casteller_tags'] = Humans::readEventColumn($event, 'casteller_tags', 'right');
            $array_event['start_date'] = Humans::parseDate($event->getStartDate());

            $now = Carbon::now();

            $array_event['buttons'] = '';
            if ($user->can('edit events')) {
                $array_event['buttons'] = '<a href="'.route('events.edit', $eventId).'" class="btn btn-warning btn-action mr-1"><i class="fa-solid fa-pencil"></i></a>';
            }
            $array_event['buttons'] .= '<a href="'.route('event.attendance', $eventId).'" class="btn btn-primary btn-action mr-1"><i class="fa-solid fa-users"></i></a>';

            if ($boardsEnabled) {
                if ($user->can('view boards')) {
                    if ($event->hasAttachedBoards()) {
                        $array_event['buttons'] .= '<a href="'.route('event.board', ['event' => $eventId]).'" class="btn btn-primary btn-board btn-action mr-1"><img src="'.asset('media/img/ico_pinya_o3.svg').'" style="width: 22px;" alt=""></a>';
                    } else {
                        $array_event['buttons'] .= '<button class="btn btn-alt-primary btn-attach-board btn-board btn-action mr-1" data-event_id="'.$eventId.'"><img src="'.asset('media/img/ico_pinya_o3.svg').'" style="width: 22px;" alt=""></button>';
                    }
                    $class = ($event->hasRondes()) ? 'btn-primary' : 'btn-alt-primary';
                    $array_event['buttons'] .= '<a href="'.route('event.rondes', ['event' => $eventId]).'" class="btn btn-rondes '.$class.' btn-action mr-1"><i class="fa-solid fa-list-ol"></i></a>';

                }
            }

            $array_event['buttons'] .= '<a href="'.route('event.attendance.verify', $eventId).'" class="btn btn-primary btn-action mr-1" target="_blank"><i class="fa-solid fa-clipboard-user"></i></a>';
            $data->data[] = $array_event;
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /** create an Event */
    public function getCreate(): View
    {

        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $colla = $this->user()->getColla();
        $data_content['type_add'] = 'ONE';
        $data_content['tags'] = $colla->getTags(TypeTags::EVENTS);
        $data_content['tags_casteller'] = $colla->getTags(TypeTags::CASTELLERS);
        $data_content['attendance_answers'] = $colla->getTags(TypeTags::ATTENDANCE);
        $data_content['types'] = Event::getTypes();

        return view('events.create', $data_content);
    }

    /**
     * TODO Refactor manager->factory
     * Store a single Event
     */
    public function postStoreEvent(EventsManager $eventsManager, Request $request): RedirectResponse
    {
        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|max:100|min:3',
            'address' => 'nullable|max:255|min:3',
            'duration' => 'required|numeric',
            'start_date' => 'required',
            'hour' => 'required|numeric',
            'min' => 'required|numeric',
            'location_link' => 'nullable|url',
        ]);

        $colla = Colla::getCurrent();

        $startDate = str_replace('/', '-', $request->start_date);
        $startDate = date('Y-m-d', strtotime($startDate)).' '.$request->hour.':'.$request->min.':00';

        //open date
        if ($request->open_date_select === 'now') {
            $openDate = Carbon::now()->toDateTimeString();
        } elseif ($request->open_date_select === 'before_starts') {
            switch ($request->open_date_mode) {
                case 'months':
                    $openDate = Carbon::parse($startDate)->subMonths(intval($request->open_date_time));
                    break;
                case 'weeks':
                    $openDate = Carbon::parse($startDate)->subWeeks(intval($request->open_date_time));
                    break;
                case 'days':
                    $openDate = Carbon::parse($startDate)->subDays(intval($request->open_date_time));
                    break;
                case 'hours':
                    $openDate = Carbon::parse($startDate)->subHours(intval($request->open_date_time));
                    break;
            }
            $openDate = $openDate->toDateTimeString();
        } else {
            $openDate = str_replace('/', '-', $request->open_date);
            $openDate = date('Y-m-d', strtotime($openDate)).' '.$request->hour_open_date.':'.$request->min_open_date.':00';
        }

        //close date
        if ($request->close_date_select === 'when_starts') {
            $closeDate = $startDate;
        } elseif ($request->close_date_select === 'before_starts') {
            switch ($request->close_date_mode) {
                case 'months':
                    $closeDate = Carbon::parse($startDate)->subMonths(intval($request->close_date_time));
                    break;
                case 'weeks':
                    $closeDate = Carbon::parse($startDate)->subWeeks(intval($request->close_date_time));
                    break;
                case 'days':
                    $closeDate = Carbon::parse($startDate)->subDays(intval($request->close_date_time));
                    break;
                case 'hours':
                    $closeDate = Carbon::parse($startDate)->subHours(intval($request->close_date_time));
                    break;
            }
            $closeDate = $closeDate->toDateTimeString();
        } else {
            $closeDate = str_replace('/', '-', $request->close_date);
            $closeDate = date('Y-m-d', strtotime($closeDate)).' '.$request->hour_close_date.':'.$request->min_close_date.':00';
        }

        if ($closeDate < $openDate) {
            Session::flash('status_ko', trans('event.close_cant_be_before_open'));

            return redirect()->back()->withInput();
        }

        $duplicateEvent = Event::filter($colla)
            ->findDuplicateByNameAndDate($request->name, $startDate)
            ->eloquentBuilder()
            ->first();

        if ($duplicateEvent) {
            Session::flash('status_ko', trans('event.duplicate_event'));

            return redirect()->back()->withInput();
        }

        $bag = new ParameterBag($request->except('_token'));
        (! $bag->has('visibility')) ? $bag->set('visibility', 0) : $bag->set('visibility', 1);
        (! $bag->has('companions')) ? $bag->set('companions', 0) : $bag->set('companions', 1);
        $bag->set('start_date', $startDate);
        $bag->set('open_date', $openDate);
        $bag->set('close_date', $closeDate);
        $eventsManager->createEvent($colla, $bag);

        Session::flash('status_ok', trans('event.event_added'));

        return redirect()->route('events.list');
    }

    /**Edit Event.*/
    public function getEditEvent(Event $event): View
    {

        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }
        $this->authorize('getEvent', $event);

        $colla = $this->user()->getColla();
        $data_content['event'] = $event;
        $data_content['tags'] = $colla->getTags(TypeTags::EVENTS);
        $data_content['tags_casteller'] = $colla->getTags(TypeTags::CASTELLERS);
        $data_content['attendance_answers'] = $colla->getTags(TypeTags::ATTENDANCE);
        $data_content['types'] = Event::getTypes();

        return view('events.create', $data_content);
    }

    /**
     *TODO Refactor en manager
     * Update Event
     */
    public function postUpdateEvent(EventsManager $eventsManager, Request $request, Event $event): RedirectResponse
    {
        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $this->authorize('getEvent', $event);

        $request->validate([
            'name' => 'required|max:100|min:3',
            'address' => 'nullable|max:255|min:3',
            'duration' => 'required|numeric',
            'start_date' => 'required',
            'hour' => 'required|numeric',
            'min' => 'required|numeric',
            'location_link' => 'nullable|url',
        ]);

        $startDate = str_replace('/', '-', $request->start_date);
        $startDate = date('Y-m-d', strtotime($startDate)).' '.$request->hour.':'.$request->min.':00';

        //open date
        if ($request->open_date_select === 'now') {
            $openDate = Carbon::now()->toDateTimeString();
        } elseif ($request->open_date_select === 'before_starts') {
            switch ($request->open_date_mode) {
                case 'months':
                    $openDate = Carbon::parse($startDate)->subMonths(intval($request->open_date_time));
                    break;
                case 'weeks':
                    $openDate = Carbon::parse($startDate)->subWeeks(intval($request->open_date_time));
                    break;
                case 'days':
                    $openDate = Carbon::parse($startDate)->subDays(intval($request->open_date_time));
                    break;
                case 'hours':
                    $openDate = Carbon::parse($startDate)->subHours(intval($request->open_date_time));
                    break;
            }
            $openDate = $openDate->toDateTimeString();
        } else {
            $openDate = str_replace('/', '-', $request->open_date);
            $openDate = date('Y-m-d', strtotime($openDate)).' '.$request->hour_open_date.':'.$request->min_open_date.':00';
        }

        //close date
        if ($request->close_date_select === 'when_starts') {
            $closeDate = $startDate;
        } elseif ($request->close_date_select === 'before_starts') {
            switch ($request->close_date_mode) {
                case 'months':
                    $closeDate = Carbon::parse($startDate)->subMonths(intval($request->close_date_time));
                    break;
                case 'weeks':
                    $closeDate = Carbon::parse($startDate)->subWeeks(intval($request->close_date_time));
                    break;
                case 'days':
                    $closeDate = Carbon::parse($startDate)->subDays(intval($request->close_date_time));
                    break;
                case 'hours':
                    $closeDate = Carbon::parse($startDate)->subHours(intval($request->close_date_time));
                    break;
            }
            $closeDate = $closeDate->toDateTimeString();
        } else {
            $closeDate = str_replace('/', '-', $request->close_date);
            $closeDate = date('Y-m-d', strtotime($closeDate)).' '.$request->hour_close_date.':'.$request->min_close_date.':00';
        }

        if ($closeDate < $openDate) {
            Session::flash('status_ko', trans('event.close_cant_be_before_open'));

            return redirect()->back()->withInput();
        }

        $duplicateEvent = Event::filter($event->getColla())
            ->findDuplicateByNameAndDate($request->name, $startDate, $event->getId())
            ->eloquentBuilder()
            ->first();

        if ($duplicateEvent) {
            Session::flash('status_ko', trans('event.duplicate_event'));

            return redirect()->back()->withInput();
        }

        $bag = new ParameterBag($request->except('_token'));

        (! $bag->has('visibility')) ? $bag->set('visibility', 0) : $bag->set('visibility', 1);
        (! $bag->has('companions')) ? $bag->set('companions', 0) : $bag->set('companions', 1);
        $bag->set('start_date', $startDate);
        $bag->set('open_date', $openDate);
        $bag->set('close_date', $closeDate);
        $eventsManager->updateEvent($event, $bag);

        Session::flash('status_ok', trans('event.event_updated'));

        return redirect()->route('events.list');
    }

    /** Delete event.*/
    public function postDestroyEvent(Event $event): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $this->authorize('getEvent', $event);

        $event->delete();

        Session::flash('status_ok', trans('event.event_destroyed'));

        return redirect()->route('events.list');
    }

    public function postAssignToMultievent(Request $request, MultieventManager $multieventManager): RedirectResponse
    {
        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        $selectedEvents = json_decode($request->selected_events, true);

        if (empty($selectedEvents)) {
            Session::flash('status_ko', trans('event.no_events_selected'));

            return redirect()->route('events.list');
        }

        $events = Event::whereIn('id_event', $selectedEvents)
            ->where('colla_id', $colla->getId())
            ->get();

        if ($events->isEmpty()) {
            Session::flash('status_ko', trans('event.no_valid_events_found'));

            return redirect()->route('events.list');
        }

        $referenceEvent = $events->first();

        if ($request->multievent_option === 'new') {
            $multieventBag = new ParameterBag();
            $multieventBag->set('name', $request->multievent_name);
            $multieventBag->set('address', $referenceEvent->getAddress());
            $multieventBag->set('location_link', $referenceEvent->getLocationLink());
            $multieventBag->set('comments', $referenceEvent->getComments());
            $multieventBag->set('duration', $referenceEvent->getDuration());
            $multieventBag->set('companions', $referenceEvent->getCompanions());
            $multieventBag->set('visibility', $referenceEvent->getVisibility());
            $multieventBag->set('type', $referenceEvent->getType());
            $multieventBag->set('photo', $referenceEvent->getPhoto());

            $time = $referenceEvent->getStartDate()->format('H:i:s');
            $multieventBag->set('time', $time);

            if ($referenceEvent->hasTags()) {
                $multieventBag->set('tags', $referenceEvent->getTags()->pluck('value')->toArray());
            }

            if ($referenceEvent->hasCastellerTags()) {
                $multieventBag->set('tags_casteller', $referenceEvent->getCastellerTags()->pluck('value')->toArray());
            }

            $multievent = $multieventManager->createMultievent($colla, $multieventBag);

            $successMessage = trans('event.events_assigned_to_new_multievent');
        } else {
            $multievent = Multievent::find($request->existing_multievent_id);

            if (! $multievent) {
                Session::flash('status_ko', trans('event.multievent_not_found'));

                return redirect()->route('events.list');
            }

            $successMessage = trans('event.events_assigned_to_existing_multievent');
        }

        foreach ($events as $event) {
            $event->assignToMultievent($multievent);
        }

        Session::flash('status_ok', $successMessage);

        return redirect()->route('events.list');
    }
}
