<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Colla;

class CollaRepository
{
    public function save(Colla $colla): bool
    {
        return $colla->save();
    }

    public function fetchByShortName(string $shortName, array $with = []): ?Colla
    {
        return Colla::query()
            ->with($with)
            ->where('shortname', $shortName)
            ->first();
    }

    /*
    public function all();
    public function create(array  $data);
    public function update(array $data, $id);
    public function delete($id);
    public function find($id);
    */

}
