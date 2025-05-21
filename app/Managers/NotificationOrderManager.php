<?php

declare(strict_types=1);

namespace App\Managers;

use App\Enums\NotificationStateEnum;
use App\Jobs\EmailNotification;
use App\Jobs\TelegramNotification;
use App\NotificationOrder;

class NotificationOrderManager
{
    public function __construct() {}

    public function process(NotificationOrder $notificationOrder)
    {
        $casteller = $notificationOrder->getCasteller();
        $notification = $notificationOrder->getNotification();

        // Type: Telegram
        if ($casteller->getCastellerTelegram() != null) {
            $notificationOrder->logs()->create([
                'channel' => 'Telegram',
                'status' => NotificationStateEnum::PENDING,
            ]);
            $message = $notification->render($casteller, 'telegram');
            TelegramNotification::dispatch($notificationOrder, $casteller, $message);
        }

        // Type: Email: Only send if telegram is not configured
        if ($casteller->getCastellerTelegram() == null && $casteller->getEmail()) {
            $notificationOrder->logs()->create([
                'channel' => 'Mail',
                'status' => NotificationStateEnum::PENDING,
            ]);
            $message = $notification->render($casteller, 'mail');
            EmailNotification::dispatch($notificationOrder, $casteller, $notification, $message);
        }
    }
}
