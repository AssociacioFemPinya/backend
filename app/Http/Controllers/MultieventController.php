<?php

namespace App\Http\Controllers;

use App\Colla;
use App\Enums\TypeTags;
use App\Event;
use App\Managers\EventsManager;
use App\Managers\MultieventManager;
use App\Multievent;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;

class MultieventController extends Controller
{
    /**
     * Display form for creating a new multievent
     */
    public function getCreateMultievent(): View
    {
        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $colla = $this->user()->getColla();

        $data_content['type_add'] = 'GROUP';
        $data_content['tags'] = $colla->getTags(TypeTags::EVENTS);
        $data_content['tags_casteller'] = $colla->getTags(TypeTags::CASTELLERS);
        $data_content['attendance_answers'] = $colla->getTags(TypeTags::ATTENDANCE);
        $data_content['types'] = Multievent::getTypes();

        return view('multievents.create', $data_content);
    }

    /**
     * Store a new multievent and its associated events
     */
    public function postStoreMultievent(MultieventManager $multieventManager, EventsManager $eventsManager, Request $request): RedirectResponse
    {
        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|max:100|min:3',
            'address' => 'nullable|max:255|min:3',
            'duration' => 'required|numeric',
            'hour' => 'required|numeric',
            'min' => 'required|numeric',
            'location_link' => 'nullable|url',
            'event_dates' => 'required',
        ]);

        $colla = Colla::getCurrent();

        $multieventBag = new ParameterBag();
        $multieventBag->set('name', $request->name);
        $multieventBag->set('address', $request->address);
        $multieventBag->set('location_link', $request->location_link);
        $multieventBag->set('comments', $request->comments);
        $multieventBag->set('duration', $request->duration);
        $multieventBag->set('visibility', $request->has('visibility') ? 1 : 0);
        $multieventBag->set('companions', $request->has('companions') ? 1 : 0);
        $multieventBag->set('type', $request->type);

        $baseHour = $request->hour;
        $baseMinute = $request->min;
        $time = sprintf('%02d:%02d:00', $baseHour, $baseMinute);
        $multieventBag->set('time', $time);

        if ($request->has('tags')) {
            $multieventBag->set('tags', $request->tags);
        }

        if ($request->has('tags_casteller')) {
            $multieventBag->set('tags_casteller', $request->tags_casteller);
        }

        $eventDates = explode(',', $request->event_dates);
        $duplicateEvents = [];

        foreach ($eventDates as $date) {
            $date = trim($date);
            if (empty($date)) {
                continue;
            }

            $eventStartDate = str_replace('/', '-', $date);
            $eventStartDate = date('Y-m-d', strtotime($eventStartDate)).' '.$baseHour.':'.$baseMinute.':00';

            $duplicate = Event::filter($colla)
                ->findDuplicateByNameAndDate($request->name, $eventStartDate)
                ->eloquentBuilder()
                ->first();

            if ($duplicate) {
                $duplicateEvents[] = $date;
            }
        }

        if (! empty($duplicateEvents)) {
            $duplicatesFormatted = implode(', ', $duplicateEvents);
            Session::flash('status_ko', trans('event.duplicate_event').' - '.trans('general.dates').': '.$duplicatesFormatted);

            return redirect()->back()->withInput();
        }

        $multievent = $multieventManager->createMultievent($colla, $multieventBag);

        foreach ($eventDates as $date) {
            $date = trim($date);
            if (empty($date)) {
                continue;
            }

            $eventStartDate = str_replace('/', '-', $date);
            $eventStartDate = date('Y-m-d', strtotime($eventStartDate)).' '.$baseHour.':'.$baseMinute.':00';

            if ($request->open_date_select === 'now') {
                $eventOpenDate = Carbon::now()->toDateTimeString();
            } elseif ($request->open_date_select === 'before_starts') {
                switch ($request->open_date_mode) {
                    case 'months':
                        $eventOpenDate = Carbon::parse($eventStartDate)->subMonths(intval($request->open_date_time));
                        break;
                    case 'weeks':
                        $eventOpenDate = Carbon::parse($eventStartDate)->subWeeks(intval($request->open_date_time));
                        break;
                    case 'days':
                        $eventOpenDate = Carbon::parse($eventStartDate)->subDays(intval($request->open_date_time));
                        break;
                    case 'hours':
                        $eventOpenDate = Carbon::parse($eventStartDate)->subHours(intval($request->open_date_time));
                        break;
                    default:
                        $eventOpenDate = Carbon::now();
                }
                $eventOpenDate = $eventOpenDate->toDateTimeString();
            }

            if ($request->close_date_select === 'when_starts') {
                $eventCloseDate = $eventStartDate;
            } elseif ($request->close_date_select === 'before_starts') {
                switch ($request->close_date_mode) {
                    case 'months':
                        $eventCloseDate = Carbon::parse($eventStartDate)->subMonths(intval($request->close_date_time));
                        break;
                    case 'weeks':
                        $eventCloseDate = Carbon::parse($eventStartDate)->subWeeks(intval($request->close_date_time));
                        break;
                    case 'days':
                        $eventCloseDate = Carbon::parse($eventStartDate)->subDays(intval($request->close_date_time));
                        break;
                    case 'hours':
                        $eventCloseDate = Carbon::parse($eventStartDate)->subHours(intval($request->close_date_time));
                        break;
                    default:
                        $eventCloseDate = Carbon::parse($eventStartDate);
                }
                $eventCloseDate = $eventCloseDate->toDateTimeString();
            }

            if ($eventCloseDate < $eventOpenDate) {
                Session::flash('status_ko', trans('event.close_cant_be_before_open').' - '.$date);

                return redirect()->back()->withInput();
            }

            $eventBag = new ParameterBag();
            $eventBag->set('name', $request->name);
            $eventBag->set('address', $request->address);
            $eventBag->set('location_link', $request->location_link);
            $eventBag->set('comments', $request->comments);
            $eventBag->set('duration', $request->duration);
            $eventBag->set('start_date', $eventStartDate);
            $eventBag->set('open_date', $eventOpenDate);
            $eventBag->set('close_date', $eventCloseDate);
            $eventBag->set('visibility', $request->has('visibility') ? 1 : 0);
            $eventBag->set('companions', $request->has('companions') ? 1 : 0);
            $eventBag->set('type', $request->type);
            $eventBag->set('id_multievent', $multievent->getId());

            if ($request->has('tags')) {
                $eventBag->set('tags', $request->tags);
            }
            if ($request->has('tags_casteller')) {
                $eventBag->set('tags_casteller', $request->tags_casteller);
            }
            if ($request->has('answers')) {
                $eventBag->set('answers', $request->answers);
            }

            $eventsManager->createEvent($colla, $eventBag);
        }

        Session::flash('status_ok', trans('multievent.multievent_added'));

        return redirect()->route('multievents.list');
    }

    /**
     * Display form for editing a multievent
     */
    public function getEditMultievent(Multievent $multievent): View
    {
        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $this->authorize('getEvent', $multievent);

        $colla = $this->user()->getColla();
        $data_content['multievent'] = $multievent;
        $data_content['tags'] = $colla->getTags(TypeTags::EVENTS);
        $data_content['tags_casteller'] = $colla->getTags(TypeTags::CASTELLERS);
        $data_content['attendance_answers'] = $colla->getTags(TypeTags::ATTENDANCE);
        $data_content['types'] = Multievent::getTypes();

        return view('multievents.create', $data_content);
    }

    /**
     * Update a multievent and its associated events
     */
    public function postUpdateMultievent(MultieventManager $multieventManager, EventsManager $eventsManager, Request $request, Multievent $multievent): RedirectResponse
    {
        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $this->authorize('getEvent', $multievent);

        $request->validate([
            'name' => 'required|max:100|min:3',
            'address' => 'nullable|max:255|min:3',
            'duration' => 'required|numeric',
            'hour' => 'required|numeric',
            'min' => 'required|numeric',
            'location_link' => 'nullable|url',
            'event_dates' => 'required',
        ]);

        $colla = Colla::getCurrent();

        $multieventBag = new ParameterBag();
        $multieventBag->set('name', $request->name);
        $multieventBag->set('address', $request->address);
        $multieventBag->set('location_link', $request->location_link);
        $multieventBag->set('comments', $request->comments);
        $multieventBag->set('duration', $request->duration);
        $multieventBag->set('visibility', $request->has('visibility') ? 1 : 0);
        $multieventBag->set('companions', $request->has('companions') ? 1 : 0);
        $multieventBag->set('type', $request->type);

        $baseHour = $request->hour;
        $baseMinute = $request->min;
        $time = sprintf('%02d:%02d:00', $baseHour, $baseMinute);
        $multieventBag->set('time', $time);

        if ($request->has('tags')) {
            $multieventBag->set('tags', $request->tags);
        }

        if ($request->has('tags_casteller')) {
            $multieventBag->set('tags_casteller', $request->tags_casteller);
        }

        $currentEventIds = $multievent->events()->pluck('id_event')->toArray();

        $eventDates = explode(',', $request->event_dates);
        $duplicateEvents = [];

        foreach ($eventDates as $date) {
            $date = trim($date);
            if (empty($date)) {
                continue;
            }

            $eventStartDate = str_replace('/', '-', $date);
            $eventStartDate = date('Y-m-d', strtotime($eventStartDate)).' '.$baseHour.':'.$baseMinute.':00';

            $existingEventInMultievent = $multievent->events()
                ->whereRaw("DATE_FORMAT(start_date, '%d/%m/%Y') = ?", [$date])
                ->first();

            if (! $existingEventInMultievent) {
                $existingEvent = \App\Event::filter($colla)
                    ->findDuplicateByNameAndDate($request->name, $eventStartDate)
                    ->eloquentBuilder()
                    ->whereNotIn('id_event', $currentEventIds)
                    ->first();

                if ($existingEvent) {
                    $duplicateEvents[] = $date;
                }
            }
        }

        if (! empty($duplicateEvents)) {
            $duplicatesFormatted = implode(', ', $duplicateEvents);
            Session::flash('status_ko', trans('event.duplicate_event').' - '.trans('general.dates').': '.$duplicatesFormatted);

            return redirect()->back()->withInput();
        }

        $multieventManager->updateMultievent($multievent, $multieventBag);

        $eventDatesFormatted = [];
        foreach ($eventDates as $date) {
            $date = trim($date);
            if (empty($date)) {
                continue;
            }
            $eventDatesFormatted[] = $date;
        }

        foreach ($multievent->getEvents() as $event) {
            $eventDate = date('d/m/Y', strtotime($event->getStartDate()));
            if (! in_array($eventDate, $eventDatesFormatted)) {
                $event->delete();
            }
        }

        foreach ($eventDatesFormatted as $date) {
            $eventStartDate = str_replace('/', '-', $date);
            $eventStartDate = date('Y-m-d', strtotime($eventStartDate)).' '.$baseHour.':'.$baseMinute.':00';

            if ($request->open_date_select === 'now') {
                $eventOpenDate = Carbon::now()->toDateTimeString();
            } elseif ($request->open_date_select === 'before_starts') {
                switch ($request->open_date_mode) {
                    case 'months':
                        $eventOpenDate = Carbon::parse($eventStartDate)->subMonths(intval($request->open_date_time));
                        break;
                    case 'weeks':
                        $eventOpenDate = Carbon::parse($eventStartDate)->subWeeks(intval($request->open_date_time));
                        break;
                    case 'days':
                        $eventOpenDate = Carbon::parse($eventStartDate)->subDays(intval($request->open_date_time));
                        break;
                    case 'hours':
                        $eventOpenDate = Carbon::parse($eventStartDate)->subHours(intval($request->open_date_time));
                        break;
                    default:
                        $eventOpenDate = Carbon::now();
                }
                $eventOpenDate = $eventOpenDate->toDateTimeString();
            }

            if ($request->close_date_select === 'when_starts') {
                $eventCloseDate = $eventStartDate;
            } elseif ($request->close_date_select === 'before_starts') {
                switch ($request->close_date_mode) {
                    case 'months':
                        $eventCloseDate = Carbon::parse($eventStartDate)->subMonths(intval($request->close_date_time));
                        break;
                    case 'weeks':
                        $eventCloseDate = Carbon::parse($eventStartDate)->subWeeks(intval($request->close_date_time));
                        break;
                    case 'days':
                        $eventCloseDate = Carbon::parse($eventStartDate)->subDays(intval($request->close_date_time));
                        break;
                    case 'hours':
                        $eventCloseDate = Carbon::parse($eventStartDate)->subHours(intval($request->close_date_time));
                        break;
                    default:
                        $eventCloseDate = Carbon::parse($eventStartDate);
                }
                $eventCloseDate = $eventCloseDate->toDateTimeString();
            }

            if ($eventCloseDate < $eventOpenDate) {
                Session::flash('status_ko', trans('event.close_cant_be_before_open').' - '.$date);

                return redirect()->back()->withInput();
            }

            $eventBag = new ParameterBag();
            $eventBag->set('name', $request->name);
            $eventBag->set('address', $request->address);
            $eventBag->set('location_link', $request->location_link);
            $eventBag->set('comments', $request->comments);
            $eventBag->set('duration', $request->duration);
            $eventBag->set('start_date', $eventStartDate);
            $eventBag->set('open_date', $eventOpenDate);
            $eventBag->set('close_date', $eventCloseDate);
            $eventBag->set('visibility', $request->has('visibility') ? 1 : 0);
            $eventBag->set('companions', $request->has('companions') ? 1 : 0);
            $eventBag->set('type', $request->type);
            $eventBag->set('id_multievent', $multievent->getId());

            if ($request->has('tags')) {
                $eventBag->set('tags', $request->tags);
            }
            if ($request->has('tags_casteller')) {
                $eventBag->set('tags_casteller', $request->tags_casteller);
            }
            if ($request->has('answers')) {
                $eventBag->set('answers', $request->answers);
            }

            $existingEvent = $multievent->events()
                ->whereRaw("DATE_FORMAT(start_date, '%d/%m/%Y') = ?", [$date])
                ->first();

            if ($existingEvent) {
                $eventsManager->updateEvent($existingEvent, $eventBag);
            } else {
                $eventsManager->createEvent($colla, $eventBag);
            }
        }

        Session::flash('status_ok', trans('multievent.multievent_updated'));

        return redirect()->route('multievents.list');
    }

    /**
     * Delete a multievent and all its associated events
     */
    public function postDestroyMultievent(MultieventManager $multieventManager, EventsManager $eventsManager, Multievent $multievent): RedirectResponse
    {
        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $this->authorize('getEvent', $multievent);

        foreach ($multievent->getEvents() as $event) {
            $eventsManager->deleteEvent($event);
        }

        $multieventManager->deleteMultievent($multievent);

        Session::flash('status_ok', trans('multievent.multievent_destroyed'));

        return redirect()->route('multievents.list');
    }

    /**
     * Display a list of multievents
     */
    public function getList(): View
    {
        $user = $this->user();

        if (! $user->can('view events')) {
            abort(404);
        }

        $colla = $this->user()->getColla();
        $data_content['tags'] = $colla->getTags(TypeTags::EVENTS);
        $data_content['tags_event_type'] = Multievent::getTypes();

        return view('multievents.list', $data_content);
    }

    /**
     * Get list of multievents for AJAX datatable
     */
    public function postListAjax(Request $request)
    {
        $user = $this->user();

        if (! $user->can('view events')) {
            abort(404);
        }

        $colla = $user->getColla();

        $multieventFilter = Multievent::filter($colla);

        if ($request->has('tags_event_type') && $request->tags_event_type != 0) {
            $multieventFilter->withType($request->tags_event_type);
        }

        if ($request->has('tags') && ! in_array('all', $request->tags)) {
            $tagIds = $request->tags;
            $operator = $request->filter_search_type == 'AND' ? FilterSearchTypesEnum::AND : FilterSearchTypesEnum::OR;
            $multieventFilter->withTags($tagIds, $operator);
        }

        if ($request->has('casteller_tags') && is_array($request->casteller_tags) && count($request->casteller_tags) > 0) {
            $multieventFilter->withCastellerTags($request->casteller_tags);
        }

        $data = $multieventFilter->datatablesFilter($request, ['multievents.name']);

        $multievents = $multieventFilter->eloquentBuilder()
            ->with(['tags', 'castellerTags', 'events'])
            ->get();

        foreach ($multievents as $multievent) {
            $buttons = '';
            if ($user->can('edit events')) {
                $buttons .= '<a href="'.route('multievents.edit', $multievent->getId()).'" class="btn btn-warning btn-action mr-1"><i class="fa-solid fa-pencil"></i></a>';
            }

            $tags = '';
            if ($multievent->getTags()->isNotEmpty()) {
                foreach ($multievent->getTags() as $tag) {
                    $tags .= '<span class="badge badge-primary">'.$tag->getName().'</span> ';
                }
            }

            $castellerTags = '';
            if ($multievent->getCastellerTags()->isNotEmpty()) {
                foreach ($multievent->getCastellerTags() as $tag) {
                    $castellerTags .= '<span class="badge badge-primary">'.$tag->getName().'</span> ';
                }
            }

            $typeName = isset(Multievent::getTypes()[$multievent->getType()]) ? Multievent::getTypes()[$multievent->getType()] : '';

            $array_multievent = [
                'name' => $multievent->getName(),
                'type' => $typeName,
                'tags' => $tags,
                'casteller_tags' => $castellerTags,
                'events_count' => $multievent->getEvents()->count(),
                'buttons' => $buttons,
            ];

            $data->data[] = $array_multievent;
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
