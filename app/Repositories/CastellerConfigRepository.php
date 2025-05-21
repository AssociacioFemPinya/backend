<?php

declare(strict_types=1);

namespace App\Repositories;

use App\CastellerConfig;

final class CastellerConfigRepository extends BaseRepository
{
    public function fetchByCastellerId(int $castellerId, array $with = []): ?CastellerConfig
    {
        return CastellerConfig::query()
            ->with($with)
            ->where('casteller_id', $collaId)
            ->first();
    }

    public function save(CastellerConfig $castellerConfig): bool
    {
        return $castellerConfig->save();
    }
}
