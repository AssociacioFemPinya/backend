<?php

declare(strict_types=1);

namespace App\Repositories;

use App\AuthConfig;
use App\Casteller;
use Illuminate\Support\Str;

class AuthConfigRepository extends BaseRepository
{
    public static function create_auth_token(Casteller $casteller)
    {
        $authConfig = AuthConfig::firstOrNew(['casteller_id' => $casteller->getId()]);
        if (! is_null($authConfig->auth_token)) {
            return;
        }

        $token = Str::random(60);
        $authConfig->auth_token = $token;
        $authConfig->save();
    }

    public function save(AuthConfig $authConfig): bool
    {
        return $authConfig->save();
    }

    public function delete(AuthConfig $authConfig): bool
    {
        return $authConfig->delete();
    }
}
