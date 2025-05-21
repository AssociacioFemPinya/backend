<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

abstract class BaseRepository
{
    public function start(): void
    {
        DB::beginTransaction();
    }

    public function success(): void
    {
        DB::commit();
    }

    public function fail(): void
    {
        DB::rollBack();
    }
}
