<?php

declare(strict_types=1);

namespace App\Managers;

use App\Casteller;
use App\Colla;
use App\Factories\CastellerConfigFactory;
use App\Factories\CastellerFactory;
use App\Repositories\CastellerConfigRepository;
use App\Repositories\CastellerRepository;
use App\Services\Filters\CastellersFilter;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\ParameterBag;

class CastellersManager
{
    private CastellerRepository $repository;

    private CastellerConfigRepository $castellerConfigRepository;

    public function __construct(CastellerRepository $repository, CastellerConfigRepository $castellerConfigRepository)
    {
        $this->repository = $repository;
        $this->castellerConfigRepository = $castellerConfigRepository;
    }

    public function fetchFromTags(CastellersFilter $filter): Collection
    {
        return $this->repository->fetchFromTags($filter);
    }

    public function createCasteller(Colla $colla, ParameterBag $bag): Casteller
    {
        $casteller = CastellerFactory::make($colla->getId(), $bag);
        $this->repository->save($casteller);
        $castellerConfig = CastellerConfigFactory::make($casteller->getId(), new ParameterBag());
        $this->castellerConfigRepository->save($castellerConfig);

        return $casteller;
    }

    public function updateCasteller(Casteller $casteller, ParameterBag $bag): Casteller
    {
        $casteller = CastellerFactory::update($casteller, $bag);
        $this->repository->save($casteller);

        return $casteller;
    }
}
