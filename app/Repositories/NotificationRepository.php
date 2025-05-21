<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Notification;

class NotificationRepository extends BaseRepository
{
    public function save(Notification $notification): bool
    {
        return $notification->save();
    }

    public function delete(Notification $notification): bool
    {
        return $notification->delete();
    }

    public static function addOrUpdateTags(Notification $notification, ?array $tags)
    {
        $notification->tags()->sync($tags, true);
    }

    public static function addCastellers(Notification $notification, ?array $castellers)
    {
        // Once the assignation of one notification to N castellers has been made,
        // it can't be re-assigned (otherwise we would re-trigger notifications).
        if ($notification->orders()->count() == 0) {
            foreach ($castellers as $castellerId) {
                $notification->orders()->create([
                    'casteller_id' => $castellerId,
                ]);
            }
        }
    }
}
