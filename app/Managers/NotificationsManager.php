<?php

declare(strict_types=1);

namespace App\Managers;

use App\Colla;
use App\Factories\NotificationFactory;
use App\Notification;
use App\Repositories\NotificationRepository;
use Symfony\Component\HttpFoundation\ParameterBag;

class NotificationsManager
{
    private NotificationRepository $repository;

    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new notification.
     *
     * @param  ParameterBag  $bag
     */
    public function createNotification(Colla $colla, $parameterBag): Notification
    {
        $notification = NotificationFactory::make($colla->getId(), $parameterBag);
        $this->repository->save($notification);

        return $notification;
    }
}
