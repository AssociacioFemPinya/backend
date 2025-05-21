<?php

namespace App\DataTables;

use App\Notification;
use App\NotificationLog;
use App\Services\Filters\NotificationLogsFilter;
use Illuminate\Http\Request;

class RegisterNotifications extends BaseDataTable
{
    const route = 'notifications.register.list-ajax';

    /**
     * RegisterNotifications constructor. If notification is specified, it will draw only the logs from that notification
     *
     * @return RegisterNotifications
     */
    public function __construct(?Notification $notification = null)
    {
        $this
            ->setId('notifications')
            ->setUrl($notification != null ? route(self::route, $notification->getId()) : route(self::route))
            ->setTitle(trans('notifications.logs'))
            ->setOrder(0)
            ->setOrderDir('desc')
            ->setSearcheableColumns(['notifications.title', 'castellers.alias']);

        if ($notification != null) {
            $this->setEmbedded(true);
        }

        $this->addColumn(name: 'created_at', title: trans('notifications.data'), orderable: true);
        $this->addColumn(name: 'casteller_id', title: trans('casteller.alias'));
        if ($notification == null) {
            $this->addColumn(name: 'notification_id', title: trans('notifications.title'));
        }
        $this->addColumn(name: 'channel', title: trans('notifications.channel'));
        $this->addColumn(name: 'status', title: trans('notifications.state'));
        if ($notification == null) {
            $this->addColumn(name: 'buttons', title: '#');
        }
    }

    public function render(Request $request, NotificationLogsFilter $notificationLogsFilter): \stdClass
    {
        return $this->renderRows(function (NotificationLog $log) {
            return [
                'created_at' => $log->getCreatedAt(),
                'casteller_id' => $log->getCasteller()->getAlias(),
                'notification_id' => $log->getNotification()->getTitle(),
                'channel' => $log->channel,
                'status' => trans('notifications.'.strtolower($log->getStatusStr())),
                'buttons' => $this->addViewButton(a_href: route('notifications.details', $log->getNotification())),
            ];
        }, $request, $notificationLogsFilter);
    }
}
