<?php

declare(strict_types=1);

namespace App\Repositories;

use App\CollaConfig;

final class CollaConfigRepository extends BaseRepository
{
    public function fetchByCollaId(int $collaId, array $with = []): ?CollaConfig
    {
        return CollaConfig::query()
            ->with($with)
            ->where('colla_id', $collaId)
            ->first();
    }

    public function save(CollaConfig $collaConfig): bool
    {
        return $collaConfig->save();
    }
}
