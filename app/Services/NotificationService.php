<?php

declare(strict_types=1);

namespace App\Services;

use App\Casteller;
use App\Colla;
use App\Enums\CastellersStatusEnum;
use App\Enums\FilterSearchTypesEnum;
use App\Enums\NotificationTypeEnum;
use App\Event;
use App\Events\NotificationReady;
use App\Managers\NotificationsManager;
use App\Notification;
use App\Repositories\NotificationRepository;
use App\User;
use Symfony\Component\HttpFoundation\ParameterBag;

class NotificationService
{
    private static function notify(
        Colla $colla,
        string $title,
        array $data,
        string $template,
        array $castellersIds,
        int $type = NotificationTypeEnum::MESSAGE,
        bool $visible = true,
        ?int $userId = null,
        ?int $castellerId = null
    ): Notification {
        $bag = new ParameterBag([
            'title' => $title,
            'data' => serialize($data),
            'template' => $template,
            'type' => $type,
            'visible' => $visible,
            'user_id' => $userId,
            'casteller_id' => $castellerId,
        ]);

        $notificationRepository = new NotificationRepository();
        $notificationManager = new NotificationsManager($notificationRepository);
        $notification = $notificationManager->createNotification($colla, $bag);

        event(new NotificationReady($notification, $castellersIds));

        return $notification;
    }

    public static function sendMessage(
        Colla $colla,
        string $title,
        string $message,
        ?int $userId = null,
        ?int $castellerId = null,
        array $tags = [],
        string $includedSearchType = FilterSearchTypesEnum::AND,
        int $type = NotificationTypeEnum::MESSAGE
    ): Notification {
        $castellersIds = Casteller::filter($colla)
            ->withTags($tags, $includedSearchType)
            ->withStatus(CastellersStatusEnum::ActiveAll())
            ->getCastellerIds();

        return self::notify(
            colla: $colla,
            title: $title,
            data: ['message' => $message],
            template: 'message',
            castellersIds: $castellersIds,
            type: $type,
            userId: $userId,
            castellerId: $castellerId
        );
    }

    public static function sendAttendanceReminder(
        Event $event,
        ?Casteller $casteller = null,
        array $tags = [],
        ?string $customMessage = null,
        ?User $user = null
    ): Notification {
        $castellersIds = Casteller::filter($event->getColla())
            ->withMissingAttendance($event)
            ->withTags($tags)
            ->withStatus(CastellersStatusEnum::ActiveAll())
            ->getCastellerIds();

        $notification = self::notify(
            colla: $event->getColla(),
            title: trans('notifications.you_have_got_one_event_missing_attendance').': '.$event->getName(),
            data: [
                'eventId' => $event->getId(),
                'eventName' => $event->getName(),
                'eventStartDate' => $event->getStartDate(),
                'eventCloseDate' => $event->getCloseDate(),
                'customMessage' => $customMessage,
            ],
            template: 'event_missing_attendance_reminder',
            castellersIds: $castellersIds,
            type: NotificationTypeEnum::REMINDER,
            userId: $user?->getId(),
            castellerId: $casteller?->getId()
        );

        $event->notifications()->attach($notification->getId());

        return $notification;
    }
}
