<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Period;

class PeriodRepository extends BaseRepository
{
    public function save(Period $period): bool
    {
        return $period->save();
    }

    public function delete(Period $period): bool
    {
        return $period->delete();
    }
}
