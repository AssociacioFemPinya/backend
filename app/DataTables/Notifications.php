<?php

// app/DataTables/UsersDataTable.php

namespace App\DataTables;

use App\Notification;
use App\Services\Filters\NotificationsFilter;
use Illuminate\Http\Request;

class Notifications extends BaseDataTable
{
    public function __construct()
    {
        $this
            ->setId('notifications')
            ->setUrl(route('notifications.messages.list-ajax'))
            ->setTitle(trans('notifications.messages'))
            ->setOrder(0)
            ->setOrderDir('desc')
            ->setSearcheableColumns(['notifications.title'])
            ->addColumn(name: 'created_at', title: trans('notifications.data'), orderable: true)
            ->addColumn(name: 'message', title: trans('notifications.body'))
            ->addColumn(name: 'buttons', title: '#');
    }

    public function render(Request $request, NotificationsFilter $notificationsFilter): \stdClass
    {
        return $this->renderRows(function (Notification $notification) {
            return [
                'created_at' => $notification->getCreatedAt(),
                'message' => (strlen($notification->render()) >= 100) ? substr($notification->render(), 0, 100).'...' : $notification->render(),
                'buttons' => $this->addViewButton(a_href: route('notifications.details', $notification)),
            ];
        }, $request, $notificationsFilter);
    }
}
