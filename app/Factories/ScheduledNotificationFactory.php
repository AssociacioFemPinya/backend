<?php

declare(strict_types=1);

namespace App\Factories;

use App\Helpers\DateHelper;
use App\ScheduledNotification;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\ParameterBag;

class ScheduledNotificationFactory
{
    public static function make(int $collaId, ParameterBag $bag): ScheduledNotification
    {
        $scheduled_notification = new ScheduledNotification();
        $scheduled_notification->setAttribute('colla_id', $collaId);

        return self::update($scheduled_notification, $bag);
    }

    public static function update(ScheduledNotification $scheduled_notification, ParameterBag $bag): ScheduledNotification
    {
        if ($bag->has('title')) {
            $scheduled_notification->setTitle($bag->get('title'));
        }
        if ($bag->has('body')) {
            $scheduled_notification->setBody($bag->get('body'));
        }
        if ($bag->has('type')) {
            $scheduled_notification->setType($bag->getInt('type'));
        }
        if ($bag->has('user_id')) {
            $scheduled_notification->setUserId($bag->get('user_id'));
        }
        if ($bag->has('notification_id')) {
            $scheduled_notification->setAttribute('notification_id', $bag->get('notification_id'));
        }
        if ($bag->has('notification_date')) {
            if ($bag->get('notification_date') == null) {
                $scheduled_notification->setNotificationDate(Carbon::now());
            } else {
                $scheduled_notification->setNotificationDate(DateHelper::parseDateTime(
                    $bag->get('notification_date'),
                    $bag->get('hour_notification_date'),
                    $bag->get('min_notification_date')
                ));
            }
        }
        if ($bag->has('filter_search_type')) {
            $scheduled_notification->setFilterSearchType($bag->get('filter_search_type'));
        }

        return $scheduled_notification;
    }
}
