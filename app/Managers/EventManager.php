<?php

declare(strict_types=1);

namespace App\Managers;

use App\Repositories\EventRepository;

class EventManager
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {

        $this->eventRepository = $eventRepository;
    }
}
