<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Event;

class EventRepository extends BaseRepository
{
    public function save(Event $event): bool
    {
        return $event->save();
    }

    public function delete(Event $event): bool
    {
        return $event->delete();
    }
}
