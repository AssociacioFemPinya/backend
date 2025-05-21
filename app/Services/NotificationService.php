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
    private static function notify(Colla $colla, string $title, string $data, string $template, array $castellers_ids, int $type = NotificationTypeEnum::MESSAGE, $visible = true, $userId = null, $castellerId = null): Notification
    {
        $bag = new ParameterBag([
            'title' => $title,
            'data' => $data,
            'template' => $template,
            'type' => $type,
            'visible' => $visible,
            'user_id' => $userId,
            'casteller_id' => $castellerId,
        ]);

        $notificationRepository = new NotificationRepository();
        $notificationManager = new NotificationsManager($notificationRepository);
        $notification = $notificationManager->createNotification($colla, $bag);
        event(new NotificationReady($notification, $castellers_ids));

        return $notification;
    }

    public static function SendMessage(Colla $colla, string $title, string $message, $userId = null, $castellerId = null, array $tags = [], string $includedSearchType = FilterSearchTypesEnum::AND, int $type = NotificationTypeEnum::MESSAGE): Notification
    {
        return self::notify(
            colla: $colla,
            title: $title,
            data: serialize(['message' => $message]),
            template: 'message',
            castellers_ids: Casteller::filter($colla)->withTags($tags, $includedSearchType)->withStatus(CastellersStatusEnum::ActiveAll())->getCastellerIds(),
            type: $type,
            userId: $userId,
            castellerId: $castellerId,
        );
    }

    public static function SendAttendanceReminder(Event $event, ?Casteller $casteller = null, array $tags = [], ?string $custom_message = null, ?User $user = null)
    {
        $notification = self::notify(
            colla: $event->getColla(),
            title: trans('notifications.you_have_got_one_event_missing_attendance').': '.$event->getName(),
            data: serialize([
                'eventName' => $event->getName(),
                'eventStartDate' => $event->getStartDate(),
                'eventCloseDate' => $event->getCloseDate(),
                'customMessage' => $custom_message,
            ]),
            template: 'event_missing_attendance_reminder',
            castellers_ids: Casteller::filter($event->getColla())->withMissingAttendance($event)->withTags($tags)->withStatus(CastellersStatusEnum::ActiveAll())->getCastellerIds(),
            type: NotificationTypeEnum::REMINDER,
            userId: $user ? $user->getId() : null,
            castellerId: $casteller ? $casteller->getId() : null,
        );

        $event->notifications()->attach($notification->getId());

        return $notification;
    }
}
