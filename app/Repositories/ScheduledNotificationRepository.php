<?php

declare(strict_types=1);

namespace App\Repositories;

use App\ScheduledNotification;

class ScheduledNotificationRepository extends BaseRepository
{
    public function save(ScheduledNotification $scheduled_notification): bool
    {
        return $scheduled_notification->save();
    }

    public function delete(ScheduledNotification $scheduled_notification): bool
    {
        return $scheduled_notification->delete();
    }

    public static function addOrUpdateTags(ScheduledNotification $scheduled_notification, ?array $tags)
    {
        $scheduled_notification->tags()->sync($tags, true);
    }
}
