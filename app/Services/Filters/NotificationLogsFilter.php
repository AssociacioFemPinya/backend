<?php

declare(strict_types=1);

namespace App\Services\Filters;

use App\Colla;
use App\Enums\NotificationStateEnum;
use App\Notification;
use App\NotificationLog;

class NotificationLogsFilter extends BaseFilter
{
    public function __construct(Colla $colla)
    {
        parent::__construct($this->eloquentBuilder = NotificationLog::query()->leftJoin(
            'notification_order',
            'notification_order.id_notification_order',
            '=',
            'notification_log.notification_order_id')
            ->leftJoin(
                'castellers',
                'castellers.id_casteller',
                '=',
                'notification_order.casteller_id')
            ->leftJoin(
                'notifications',
                'notifications.id_notification',
                '=',
                'notification_order.notification_id')
            ->where('notifications.colla_id', $colla->getId())
            ->with('notification_order.casteller')
            ->with('notification_order.notification')
            ->select('notification_log.*'));
    }

    public function fromNotification(Notification $notification): self
    {
        $this->eloquentBuilder
            ->where('notifications.id_notification', '=', $notification->getId());

        return $this;
    }

    public function withoutPending(): self
    {
        $this->eloquentBuilder
            ->where('notification_log.status', '!=', NotificationStateEnum::PENDING);

        return $this;
    }
}
