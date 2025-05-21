<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Casteller;
use App\Colla;
use App\Enums\AttendanceStatus;
use App\Enums\NotificationTypeEnum;
use App\Event;
use App\Notification;
use App\Services\CollaStats;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /***/
    public function index()
    {
        return redirect('dashboard')->send();
    }

    /** Show the application dashboard to the user.  */
    public function dashboard(?int $eventId = null)
    {
        $data_content['colla'] = Colla::getCurrent();
        $colla = Colla::getCurrent();

        $limitEvents = 10;

        $nextEvents = Event::filter($colla)->liveOrUpcoming()->visible()->eloquentBuilder()
            ->orderBy('start_date', 'asc')
            ->limit($limitEvents)
            ->get();
        $data_content['events'] = $nextEvents;

        if (! $nextEvents->isEmpty()) {

            if (is_null($eventId)) {
                $eventId = $nextEvents->first()->getId();
            } elseif (! $nextEvents->contains('id_event', $eventId)) {
                return redirect('dashboard')->send();
            }

            $displayEvent = Event::findOrFail($eventId);

            $data_content['displayEvent'] = $displayEvent;

            $attendancesAll = Attendance::filter($colla)
                ->fromEvent($displayEvent)
                ->getStatusAndAlias();

            $attendances['ok'] = [];
            $attendances['nok'] = [];
            $attendances['unknown'] = [];

            if (! empty($attendancesAll)) {

                foreach ($attendancesAll as $attendance) {

                    switch ($attendance['status']) {
                        case AttendanceStatus::YES:
                            $attendances['ok'][] = $attendance['alias'];
                            break;
                        case AttendanceStatus::NO:
                            $attendances['nok'][] = $attendance['alias'];
                            break;
                        case AttendanceStatus::UNKNOWN:
                            $attendances['unknown'][] = $attendance['alias'];
                            break;
                    }
                }
            }

            $eventboards = $displayEvent->boardsEvent()->get();

        }

        $data_content['attendances'] = $attendances ?? null;
        $data_content['eventboards'] = $eventboards ?? null;
        $data_content['displayEvent'] = $displayEvent ?? null;

        $column_order = 'created_at';
        $dir = 'DESC';
        $limit = 2;
        $notificationsAll = Notification::filter($colla)
            ->withTypes([NotificationTypeEnum::MESSAGE, NotificationTypeEnum::SCHEDULED_MESSAGE])
            ->visible()
            ->eloquentBuilder()
            ->orderBy($column_order, $dir)
            ->limit($limit)
            ->get();

        foreach ($notificationsAll as $notificationAll) {
            $notification = [];
            $notification['id'] = $notificationAll->getId();
            $notification['title'] = $notificationAll->getTitle();
            $notification['date'] = $notificationAll->getCreatedAt();
            $notification['body'] = (strlen($notificationAll->render()) >= 100) ? substr($notificationAll->render(), 0, 100).'...' : $notificationAll->render();
            if ($notificationAll->getUserId()) {
                $author = User::find($notificationAll->getUserId());
                $notification['authorName'] = $author?->getName();
                $notification['authorPhoto'] = $author?->getProfileImage();
            } else {
                $author = Casteller::find($notificationAll->getCastellerId());
                $notification['authorName'] = $author?->getDisplayName();
                $notification['authorPhoto'] = $author?->getProfileImage();
            }

            $notifications[] = $notification;
        }

        $data_content['notifications'] = $notifications ?? null;

        $data_content['boardsColla'] = $colla->getBoards();

        $comptadors = new CollaStats($colla);
        $compta['events'] = $comptadors->eventsCounter();
        $compta['castellers'] = $comptadors->castellersCounter();
        $compta['maxCastellers'] = $colla->getMaxMembers();
        $compta['users'] = $comptadors->usersCounter();
        $compta['membersTelegram'] = $comptadors->membersTelegramCounter();
        $compta['membersWeb'] = $comptadors->membersWebCounter();
        $compta['notifications'] = $comptadors->notificationsCounter();

        $data_content['compta'] = $compta;

        return view('dashboard.dashboard', $data_content);
    }
}
