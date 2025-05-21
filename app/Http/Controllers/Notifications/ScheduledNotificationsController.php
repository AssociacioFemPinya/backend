<?php

namespace App\Http\Controllers\Notifications;

use App\Colla;
use App\DataTables\ScheduledNotifications;
use App\Enums\NotificationTypeEnum;
use App\Enums\TypeTags;
use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Managers\ScheduledNotificationsManager;
use App\ScheduledNotification;
use App\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\ParameterBag;

class ScheduledNotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function getList()
    {
        $user = $this->user();
        if (! $user->can('view notifications') && ! $user->can('edit notifications')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        $datatable = new ScheduledNotifications();

        return view('notifications.scheduled_notifications.list', compact('datatable', 'colla'));
    }

    /**
     * Show the form for creating a notification resource.
     *
     * @return Factory|View
     */
    public function getCreate()
    {
        $user = $this->user();
        $colla = Colla::getCurrent();

        if (! $user->can('edit notifications')) {
            abort(404);
        }
        $data_content['tags'] = $colla->getTags(TypeTags::CASTELLERS);

        return view('notifications.scheduled_notifications.create', $data_content);
    }

    /**
     * Display the specified resource.
     *
     * @return Factory|View
     *
     * @throws AuthorizationException
     */
    public function getDetails(ScheduledNotification $scheduledNotification)
    {
        $user = $this->user();

        if (! $user->can('view notifications') && ! $user->can('edit notifications')) {
            abort(404);
        }
        $this->authorize('getScheduledNotification', $scheduledNotification);

        $autoria = User::findOrFail($scheduledNotification->user_id);
        $scheduledNotification->autoria = $autoria->name;

        return view('notifications.scheduled_notifications.details', compact('scheduledNotification'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function postStoreNotification(ScheduledNotificationsManager $scheduledNotificationsManager, Request $request)
    {
        $user = $this->user();
        if (! $user->can('edit notifications')) {
            abort(404);
        }

        $request->validate([
            'title' => 'required|max:100|min:3',
            'body' => 'required|max:4096|min:3', // 4096 is the maximum amount of characters in telegram
            'notification_date' => 'required',
            'hour_notification_date' => 'required|numeric',
            'min_notification_date' => 'required|numeric',
            'visibility' => 'nullable|boolean',
        ]);

        if (DateHelper::isDateInPast($request->input('notification_date'), $request->input('hour_notification_date'), $request->input('min_notification_date'))) {
            Session::flash('status_ko', trans('notifications.notification_date_past'));

            return redirect()->back()->withInput();
        }

        $colla = Colla::getCurrent();
        $bag = new ParameterBag($request->except('_token'));
        $bag->set('type', NotificationTypeEnum::MESSAGE);
        $bag->set('user_id', $user->id_user);
        $scheduledNotificationsManager->createNotification($colla, $bag);

        Session::flash('status_ok', trans('notifications.fet'));

        return redirect(route('notifications.scheduled_notifications.list'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Factory|View
     */
    public function getEditNotification(ScheduledNotification $scheduledNotification)
    {
        $user = $this->user();
        $colla = Colla::getCurrent();

        if (! $user->can('edit notifications')) {
            abort(404);
        }
        $this->authorize('getScheduledNotification', $scheduledNotification);

        $data_content['notification'] = $scheduledNotification;
        $data_content['redactor'] = User::findOrFail($scheduledNotification->user_id);
        $data_content['tags'] = $colla->getTags(TypeTags::CASTELLERS);

        return view('notifications.scheduled_notifications.create', $data_content);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ScheduledNotificationsManager  $notificationsManager
     * @return RedirectResponse|Redirector
     */
    public function postUpdateNotification(ScheduledNotificationsManager $scheduledNotificationsManager, Request $request, ScheduledNotification $scheduledNotification)
    {
        $user = $this->user();
        if (! $user->can('edit notifications')) {
            abort(404);
        }
        $this->authorize('getScheduledNotification', $scheduledNotification);

        $request->validate([
            'title' => 'required|max:100|min:3',
            'body' => 'required|max:4096|min:3', // 4096 is the maximum amount of characters in telegram
            'notification_date' => 'required',
            'hour_notification_date' => 'required|numeric',
            'min_notification_date' => 'required|numeric',
            'visibility' => 'nullable|boolean',
        ]);

        if (DateHelper::isDateInPast($request->input('notification_date'), $request->input('hour_notification_date'), $request->input('min_notification_date'))) {
            Session::flash('status_ko', trans('notifications.notification_date_past'));

            return redirect()->back()->withInput();
        }

        $bag = new ParameterBag($request->except('_token'));
        $bag->set('type', NotificationTypeEnum::MESSAGE);
        $bag->set('user_id', $user->id_user);
        $scheduledNotificationsManager->updateNotification($scheduledNotification, $bag);

        Session::flash('status_ok', trans('notifications.editada'));

        return redirect(route('notifications.scheduled_notifications.list'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return RedirectResponse|Redirector
     *
     * @throws Exception
     */
    public function postDestroyNotification(ScheduledNotification $scheduledNotification)
    {
        $user = $this->user();
        if (! $user->can('edit notifications')) {
            abort(404);
        }
        $this->authorize('getScheduledNotification', $scheduledNotification);

        $scheduledNotification->delete();

        Session::flash('status_ok', trans('event.deleted'));

        return redirect(route('notifications.scheduled_notifications.list'));
    }

    /** get $time = upcoming/past events via AJAX
     */
    public function postListAjax(Request $request)
    {
        $user = $this->user();
        if (! $user->can('view notifications') && ! $user->can('edit notifications')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        $datatable = new ScheduledNotifications();
        $data = $datatable->render($user, $request, ScheduledNotification::filter($colla));
        echo json_encode($data);
    }
}
