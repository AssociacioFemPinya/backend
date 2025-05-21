<?php

declare(strict_types=1);

namespace App\Repositories;

use App\User;

class UserRepository extends BaseRepository
{
    public function save(User $user): bool
    {
        return $user->save();
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
