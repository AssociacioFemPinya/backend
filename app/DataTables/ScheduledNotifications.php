<?php

// app/DataTables/UsersDataTable.php

namespace App\DataTables;

use App\ScheduledNotification;
use App\Services\Filters\ScheduledNotificationsFilter;
use Illuminate\Http\Request;

class ScheduledNotifications extends BaseDataTable
{
    public function __construct()
    {
        $this
            ->setId('notifications')
            ->setUrl(route('notifications.scheduled_notifications.list-ajax'))
            ->setTitle(trans('notifications.scheduled_notifications'))
            ->setOrder(0)
            ->setOrderDir('desc')
            ->setSearcheableColumns(['scheduled_notifications.title'])
            ->addColumn(name: 'notification_date', title: trans('notifications.data'), orderable: true)
            ->addColumn(name: 'title', title: trans('notifications.title'))
            ->addColumn(name: 'body', title: trans('notifications.body'))
            ->addColumn(name: 'buttons', title: '#');
    }

    public function render($user, Request $request, ScheduledNotificationsFilter $scheduledNotificationsFilter): \stdClass
    {
        $params = [
            'user_can_edit_notifications' => $user->can('edit notifications'),
        ];

        return $this->renderRows(function (ScheduledNotification $scheduledNotification, array $params) {

            $buttons = $scheduledNotification->hasBeenNotified() ? $this->addViewButton(a_href: route('notifications.scheduled_notifications.details', $scheduledNotification)) : '';
            $buttons .= $scheduledNotification->hasBeenNotified() ? $this->addNotificationButton(a_href: route('notifications.details', $scheduledNotification->getNotification())) : '';
            $buttons .= $params['user_can_edit_notifications'] && ! $scheduledNotification->hasBeenNotified() ? $this->addEditButton(a_href: route('notifications.scheduled_notifications.edit', $scheduledNotification->getId())) : '';
            $buttons .= $params['user_can_edit_notifications'] && ! $scheduledNotification->hasBeenNotified() ? $this->addDeleteButton(data: ['id_scheduled_notification' => $scheduledNotification->getId()], btn_class: ['btn-delete-notification']) : '';

            return [
                'title' => $scheduledNotification->getTitle(),
                'body' => (strlen($scheduledNotification->getBody()) >= 100) ? substr($scheduledNotification->getBody(), 0, 100).'...' : $scheduledNotification->getBody(),
                'notification_date' => $scheduledNotification->getNotificationDate(),
                'buttons' => $buttons,
            ];

        }, $request, $scheduledNotificationsFilter, $params);
    }
}
