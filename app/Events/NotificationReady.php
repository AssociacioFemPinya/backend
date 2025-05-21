<?php

declare(strict_types=1);

namespace App\Events;

use App\Notification;
use Illuminate\Support\Facades\Event;

class NotificationReady extends Event
{
    public Notification $notification;

    public array $castellersIds;

    public function __construct(Notification $notification, array $castellersIds)
    {
        $this->notification = $notification;
        $this->castellersIds = $castellersIds;
    }
}
