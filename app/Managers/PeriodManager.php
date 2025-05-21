<?php

declare(strict_types=1);

namespace App\Managers;

use App\Colla;
use App\Factories\PeriodFactory;
use App\Period;
use App\Repositories\PeriodRepository;
use Symfony\Component\HttpFoundation\ParameterBag;

class PeriodManager
{
    private PeriodRepository $repository;

    public function __construct(PeriodRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createPeriod(Colla $colla, ParameterBag $bag): Period
    {
        $period = PeriodFactory::make($colla->getId(), $bag);
        $this->repository->save($period);

        return $period;
    }

    public function updatePeriod(Period $period, ParameterBag $bag): Period
    {
        $period = PeriodFactory::update($period, $bag);
        $this->repository->save($period);

        return $period;
    }

    public function deletePeriod(Period $period)
    {
        $this->repository->delete($period);

    }
}
