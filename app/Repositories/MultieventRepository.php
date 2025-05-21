<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Multievent;

class MultieventRepository extends BaseRepository
{
    public function save(Multievent $multievent): bool
    {
        return $multievent->save();
    }

    public function delete(Multievent $multievent): bool
    {
        return $multievent->delete();
    }
}
