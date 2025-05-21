<?php

namespace App\Http\Controllers\Notifications;

use App\Colla;
use App\DataTables\Notifications as NotificationsDataTable;
use App\DataTables\RegisterNotifications as RegisterNotificationsDataTable;
use App\Enums\NotificationTypeEnum;
use App\Http\Controllers\Controller;
use App\Mail\Notification as NotificationMail;
use App\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationsController extends Controller
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
        $datatable = new NotificationsDataTable();

        return view('notifications.list', compact('datatable', 'colla'));
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
        $datatable = new NotificationsDataTable();
        $data = $datatable->render($request, Notification::filter($colla)->withTypes([NotificationTypeEnum::MESSAGE]));
        echo json_encode($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  Notification  $scheduledNotification
     * @return Factory|View
     *
     * @throws AuthorizationException
     */
    public function getDetails(Notification $notification)
    {
        $user = $this->user();

        if (! $user->can('view notifications') && ! $user->can('edit notifications')) {
            abort(404);
        }
        $this->authorize('getNotification', $notification);

        $notification->autoria = $notification->user != null ? $notification->user->getName() : '';
        $datatable = new RegisterNotificationsDataTable($notification);

        $email_view = (new NotificationMail($notification->render(), ''));

        return view('notifications.details', compact('datatable', 'notification', 'email_view'));
    }
}
