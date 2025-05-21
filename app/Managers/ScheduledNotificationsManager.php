<?php

declare(strict_types=1);

namespace App\Managers;

use App\Colla;
use App\Enums\NotificationTypeEnum;
use App\Factories\ScheduledNotificationFactory;
use App\Repositories\ScheduledNotificationRepository;
use App\ScheduledNotification;
use App\Services\NotificationService;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\ParameterBag;

class ScheduledNotificationsManager
{
    private ScheduledNotificationRepository $repository;

    public function __construct(ScheduledNotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new notification.
     */
    public function createNotification(Colla $colla, ParameterBag $bag): ScheduledNotification
    {
        $scheduled_notification = ScheduledNotificationFactory::make($colla->getId(), $bag);
        $this->repository->save($scheduled_notification);
        if ($bag->has('tags')) {
            $this->repository->addOrUpdateTags($scheduled_notification, $bag->get('tags'));
        }

        return $scheduled_notification;
    }

    /**
     * Update an existing notification.
     *
     * @param  Colla  $colla
     */
    public function updateNotification(ScheduledNotification $scheduledNotification, ParameterBag $bag): ScheduledNotification
    {
        $event = ScheduledNotificationFactory::update($scheduledNotification, $bag);
        $this->repository->save($scheduledNotification);
        if ($bag->has('tags')) {
            $this->repository->addOrUpdateTags($scheduledNotification, $bag->get('tags'));
        }

        return $event;
    }

    /**
     * Find the scheduled_notifications ready to be notified and process them.
     */
    public function processReadyNotification()
    {
        $now = Carbon::now();
        $ready_scheduled_notifications = ScheduledNotification::where('notification_id', null)->where('notification_date', '<', $now)->get();
        foreach ($ready_scheduled_notifications as $scheduled_notification) {
            $notification = NotificationService::SendMessage(
                $scheduled_notification->getColla(),
                $scheduled_notification->getTitle(),
                $scheduled_notification->getBody(),
                userId: $scheduled_notification->getUserId(),
                tags: array_column($scheduled_notification->getTags()->toArray(), 'id_tag'),
                includedSearchType: $scheduled_notification->getFilterSearchType(),
                type: NotificationTypeEnum::SCHEDULED_MESSAGE,
            );

            // Link scheduled_notification with created notification so it's not processed twice
            $bag = new ParameterBag();
            $bag->set('notification_id', $notification->getId());
            $this->updateNotification($scheduled_notification, $bag);
        }
    }
}
