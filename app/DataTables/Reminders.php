<?php

// app/DataTables/UsersDataTable.php

namespace App\DataTables;

use App\Notification;

class Reminders extends BaseDataTable
{
    public function __construct()
    {
        $this
            ->setId('notifications')
            ->setUrl(route('notifications.reminders.list-ajax'))
            ->setTitle(trans('notifications.reminders'))
            ->setOrder(0)
            ->setOrderDir('desc')
            ->setSearcheableColumns(['notifications.title'])
            ->addColumn(name: 'created_at', title: trans('notifications.data'), orderable: true)
            ->addColumn(name: 'title', title: trans('notifications.title'))
            ->addColumn(name: 'event', title: trans('notifications.event'))
            ->addColumn(name: 'buttons', title: '#');
    }

    public function render($request, $notificationsFilter): \stdClass
    {
        // uncomment when merging https://github.com/AssociacioFemPinya/fempinya3/pull/487/files
        //$notificationsFilter->with('event_notification');

        return $this->renderRows(function (Notification $notification) {
            $event = $notification->events->first();

            return [
                'created_at' => $notification->getCreatedAt(),
                'title' => $notification->getTitle(),
                'event' => $event ? $this->addHyperlink(route('event.attendance', $event->getId()), $event->getName()) : '',
                'buttons' => $this->addViewButton(a_href: route('notifications.details', $notification)),
            ];
        }, $request, $notificationsFilter);
    }
}
