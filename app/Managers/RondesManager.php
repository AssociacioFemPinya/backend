<?php

declare(strict_types=1);

namespace App\Managers;

use App\BoardEvent;
use App\Event;
use App\Factories\RondaFactory;
use App\Repositories\RondaRepository;
use App\Ronda;
use Symfony\Component\HttpFoundation\ParameterBag;

class RondesManager
{
    private RondaRepository $repository;

    public function __construct(RondaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createRonda(ParameterBag $bag, Event $event, BoardEvent $boardEvent): Ronda
    {
        $ronda = RondaFactory::make($bag, $event->getId(), $boardEvent->getId());
        $this->repository->save($ronda);

        return $ronda;
    }

    public function updateRonda(Ronda $ronda, ParameterBag $bag): Ronda
    {
        $ronda = RondaFactory::update($ronda, $bag);
        $this->repository->save($ronda);

        return $ronda;
    }

    public function deleteRonda(Ronda $ronda): void
    {
        $this->repository->delete($ronda);
    }
}
