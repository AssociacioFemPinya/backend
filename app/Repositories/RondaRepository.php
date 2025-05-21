<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Ronda;

final class RondaRepository extends BaseRepository
{
    public function save(Ronda $ronda): bool
    {
        return $ronda->save();
    }

    public function delete(Ronda $ronda): bool
    {
        return $ronda->delete();
    }
}
