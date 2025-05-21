<?php

declare(strict_types=1);

namespace App\Observers;

use App\CastellerConfig;
use App\Repositories\AuthConfigRepository;

class CastellerConfigObserver
{
    public function updated(CastellerConfig $castellerConfig)
    {
        if ($castellerConfig->isDirty('auth_token_enabled')) {
            if ($castellerConfig->getAuthTokenEnabled()) {
                AuthConfigRepository::create_auth_token($castellerConfig->casteller);
            }
        }
    }
}
