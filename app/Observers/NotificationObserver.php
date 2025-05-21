<?php

declare(strict_types=1);

namespace App\Observers;

use App\Events\NotificationReady;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificationObserver implements ShouldQueue
{
    private NotificationRepository $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * Handle a NotificationReady event.
     */
    public function ready(NotificationReady $notificationReadyEvent)
    {
        $notification = $notificationReadyEvent->notification;
        $castellersIds = $notificationReadyEvent->castellersIds;

        $this->notificationRepository->addCastellers($notification, $castellersIds);
    }
}
