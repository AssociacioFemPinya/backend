<?php

declare(strict_types=1);

namespace App\Http\Controllers\Member;

use App\Colla;
use App\DataTables\MemberNotifications;
use App\Http\Controllers\Controller;
use App\Mail\Notification as NotificationMail;
use App\Notification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $data_content = [];
        $colla = Colla::getCurrent();
        $datatable = new MemberNotifications();

        return view('members.notifications.list', compact('datatable', 'colla'));
    }

    /** Create a new notification through AJAX
     */
    public function postListAjax(Request $request)
    {
        $casteller = Auth::user()->casteller;
        $colla = Colla::getCurrent();
        $notificationsFilter = Notification::filter($colla)->withNotifiedCasteller($casteller);
        $datatable = new MemberNotifications();
        $data = $datatable->render($request, $notificationsFilter, $casteller);
        echo json_encode($data);
    }

    /** Get a modal with information about a notification through AJAX
     * @param  Request  $request
     * @return Factory|View
     */
    public function getNotificationInfoModalAjax(Notification $notification)
    {
        $casteller = Auth::user()->casteller;
        $data_content['email_view'] = (new NotificationMail($notification->render($casteller), ''));

        return view('members.modals.get-notification-info', $data_content);
    }
}
