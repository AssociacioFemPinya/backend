<?php

namespace App\Http\Controllers\Notifications;

use App\Colla;
use App\DataTables\RegisterNotifications;
use App\Http\Controllers\Controller;
use App\Notification;
use App\NotificationLog;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisterNotificationsController extends Controller
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

        $datatable = new RegisterNotifications();

        return view('notifications.register.list', compact('datatable'));
    }

    /**
     * Display notification logs through AJAX.
     */
    public function postListAjax(Request $request, ?Notification $notification = null)
    {
        $user = $this->user();

        if (! $user->can('view notifications') && ! $user->can('edit notifications')) {
            abort(404);
        }
        $this->authorize('getNotification', $notification);

        $colla = Colla::getCurrent();
        $notificationsFilter = NotificationLog::filter($colla);
        if ($notification != null) {
            $notificationsFilter
                ->fromNotification($notification)
                ->withoutPending();
        }

        $datatable = new RegisterNotifications();
        $data = $datatable->render($request, $notificationsFilter);

        echo json_encode($data);
    }
}
