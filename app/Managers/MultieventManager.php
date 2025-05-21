<?php

declare(strict_types=1);

namespace App\Managers;

use App\Colla;
use App\Factories\MultieventFactory;
use App\Multievent;
use App\Repositories\MultieventRepository;
use Symfony\Component\HttpFoundation\ParameterBag;

class MultieventManager
{
    private MultieventRepository $repository;

    public function __construct(MultieventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createMultievent(Colla $colla, ParameterBag $bag): Multievent
    {
        $multievent = MultieventFactory::make($colla->getId(), $bag);
        $this->repository->save($multievent);

        return $multievent;
    }

    public function updateMultievent(Multievent $multievent, ParameterBag $bag): Multievent
    {
        $multievent = MultieventFactory::update($multievent, $bag);
        $this->repository->save($multievent);

        return $multievent;
    }

    public function deleteMultievent(Multievent $multievent): bool
    {
        return $this->repository->delete($multievent);
    }
}
