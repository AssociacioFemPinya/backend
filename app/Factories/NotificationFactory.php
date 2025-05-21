<?php

declare(strict_types=1);

namespace App\Factories;

use App\Notification;
use Symfony\Component\HttpFoundation\ParameterBag;

class NotificationFactory
{
    public static function make(int $collaId, ParameterBag $bag): Notification
    {
        $notification = new Notification();
        $notification->setAttribute('colla_id', $collaId);

        return self::update($notification, $bag);
    }

    public static function update(Notification $notification, ParameterBag $bag): Notification
    {
        if ($bag->has('title')) {
            $notification->setTitle($bag->get('title'));
        }
        if ($bag->has('data')) {
            $notification->setData($bag->get('data'));
        }
        if ($bag->has('template')) {
            $notification->setTemplate($bag->get('template'));
        }
        if ($bag->has('type')) {
            $notification->setType($bag->getInt('type'));
        }
        if ($bag->has('user_id')) {
            $notification->setUserId($bag->getInt('user_id'));
        }
        if ($bag->has('casteller_id')) {
            $notification->setCastellerId($bag->getInt('casteller_id'));
        }
        if ($bag->has('visible')) {
            $notification->setNotificationVisible($bag->get('visible'));
        }

        return $notification;
    }
}
