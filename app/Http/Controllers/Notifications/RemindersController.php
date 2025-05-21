<?php

namespace App\Http\Controllers\Notifications;

use App\Colla;
use App\DataTables\Reminders as RemindersDataTable;
use App\Enums\NotificationTypeEnum;
use App\Http\Controllers\Controller;
use App\Notification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RemindersController extends Controller
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
        $datatable = new RemindersDataTable();

        return view('notifications.reminders.list', compact('datatable', 'colla'));
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
        $datatable = new RemindersDataTable();
        $data = $datatable->render($request, Notification::filter($colla)->withTypes([NotificationTypeEnum::REMINDER]));
        echo json_encode($data);
    }
}
