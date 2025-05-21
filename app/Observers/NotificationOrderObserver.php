<?php

declare(strict_types=1);

namespace App\Observers;

use App\Managers\NotificationOrderManager;
use App\NotificationOrder;

class NotificationOrderObserver
{
    /**
     * Reacts on a new notification created.
     * Creates the necessary notification_orders
     */
    public function created(NotificationOrder $notificationOrder)
    {
        $notificationOrderManager = new NotificationOrderManager();
        $notificationOrderManager->process($notificationOrder);
    }
}
