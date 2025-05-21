<?php

declare(strict_types=1);

namespace App\Managers;

use App\Colla;
use App\CollaConfig;
use App\Factories\CollaConfigFactory;
use App\Factories\CollaFactory;
use App\Repositories\CollaConfigRepository;
use App\Repositories\CollaRepository;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\ParameterBag;

final class CollesManager
{
    private CollaRepository $repository;

    private CollaConfigRepository $collaConfigRepository;

    public function __construct(CollaRepository $repository, CollaConfigRepository $collaConfigRepository)
    {
        $this->repository = $repository;
        $this->collaConfigRepository = $collaConfigRepository;
    }

    public function createColla(ParameterBag $bag): Colla
    {
        $colla = CollaFactory::make($bag);
        $this->repository->save($colla);
        $collaConfig = CollaConfigFactory::make($colla->getId(), new ParameterBag());
        $this->collaConfigRepository->save($collaConfig);

        return $colla;
    }

    public function updateColla(Colla $colla, ParameterBag $bag): Colla
    {
        $colla = CollaFactory::update($colla, $bag);
        $this->repository->save($colla);

        return $colla;
    }

    public function updateCollaConfig(CollaConfig $collaConfig, ParameterBag $bag): CollaConfig
    {
        if ($bag->has('public_display_enabled')) {
            if (! $collaConfig->getAes256KeyPublic()) {
                $bag->add(['aes256_key_public' => Str::random(24)]);
            }
        }

        $collaConfig = CollaConfigFactory::update($collaConfig, $bag);
        $this->collaConfigRepository->save($collaConfig);

        return $collaConfig;
    }

    public function fetchByShortName(string $shortName): ?Colla
    {
        return $this->repository->fetchByShortName($shortName);
    }
}
