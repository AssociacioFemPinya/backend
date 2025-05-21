<?php

declare(strict_types=1);

namespace App\Managers;

use App\Enums\NotificationStateEnum;
use App\Jobs\EmailNotification;
use App\Jobs\FirebaseNotification;
use App\Jobs\TelegramNotification;
use App\NotificationOrder;

class NotificationOrderManager
{
    public function __construct() {}

    public function process(NotificationOrder $notificationOrder)
    {
        $casteller = $notificationOrder->getCasteller();
        $notification = $notificationOrder->getNotification();

        // Notification order (only notify in one channel):
        // 1. Firebase
        // 2. Telegram
        // 3. Mail

        $channels = [
            'firebase' => [
                'condition' => fn () => $casteller->getCastellerConfig()->getFirebaseToken() !== null,
                'job' => fn ($message) => FirebaseNotification::dispatch($notificationOrder, $casteller, $notification, $message),
            ],
            'telegram' => [
                'condition' => fn () => $casteller->getCastellerTelegram() !== null,
                'job' => fn ($message) => TelegramNotification::dispatch($notificationOrder, $casteller, $message),
            ],
            'mail' => [
                'condition' => fn () => $casteller->getEmail(),
                'job' => fn ($message) => EmailNotification::dispatch($notificationOrder, $casteller, $notification, $message),
            ],
        ];

        foreach ($channels as $channel => $config) {
            if ($config['condition']()) {
                $notificationOrder->logs()->create([
                    'channel' => $channel,
                    'status' => NotificationStateEnum::PENDING,
                ]);
                $message = $notification->render($casteller, $channel);
                $config['job']($message);
                break;
            }
        }
    }
}
