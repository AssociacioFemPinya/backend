<?php

// app/DataTables/UsersDataTable.php

namespace App\DataTables;

use App\Casteller;
use App\Notification;
use App\Services\Filters\NotificationsFilter;
use Illuminate\Http\Request;

class MemberNotifications extends BaseDataTable
{
    public function __construct()
    {
        $this
            ->setId('notifications')
            ->setUrl(route('member.notifications.list-ajax'))
            ->setTitle(trans('notifications.notifications'))
            ->setOrder(1)
            ->setOrderDir('desc')
            ->setSearcheableColumns(['notifications.title'])

            ->addColumn(name: 'created_at', title: trans('notifications.data'), orderable: true)
            ->addColumn(name: 'title', title: trans('notifications.title'))
            ->addColumn(name: 'message', title: trans('notifications.body'))
            ->addColumn(name: 'buttons', title: '#');
    }

    public function render(Request $request, NotificationsFilter $notificationsFilter, Casteller $casteller): \stdClass
    {
        return $this->renderRows(function (Notification $notification, Casteller $casteller) {
            $notification_text = $notification->render($casteller);

            return [
                'created_at' => $notification->getCreatedAt(),
                'title' => $notification->getTitle(),
                'message' => (strlen($notification_text) >= 100) ? substr($notification_text, 0, 100).'...' : $notification_text,
                'buttons' => $this->addViewButton(data: ['id_notification' => $notification->getId()]),
            ];

        }, $request, $notificationsFilter, $casteller);
    }
}
