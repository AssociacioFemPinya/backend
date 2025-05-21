<?php

declare(strict_types=1);

namespace App\Managers;

use App\Colla;
use App\event;
use App\Factories\EventFactory;
use App\Repositories\EventRepository;
use Symfony\Component\HttpFoundation\ParameterBag;

class EventsManager
{
    private EventRepository $repository;

    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createEvent(Colla $colla, ParameterBag $bag): event
    {
        $event = EventFactory::make($colla->getId(), $bag);
        $this->repository->save($event);

        return $event;
    }

    public function updateEvent(event $event, ParameterBag $bag): event
    {
        $event = EventFactory::update($event, $bag);
        $this->repository->save($event);

        return $event;
    }

    public function deleteEvent(event $event)
    {
        $this->repository->delete($event);

    }
}
