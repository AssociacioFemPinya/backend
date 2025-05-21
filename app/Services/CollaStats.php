<?php

declare(strict_types=1);

namespace App\Services;

use App\CastellerConfig;
use App\CastellerTelegram;
use App\Colla;
use App\Notification;

class CollaStats
{
    private Colla $colla;

    public function __construct(Colla $colla)
    {
        $this->colla = $colla;
    }

    /** Num users from colla */
    public function usersCounter(): int
    {
        return $this->colla->users()->count();
    }

    /** Num events from colla */
    public function eventsCounter(): int
    {
        return $this->colla->events()->count();
    }

    /** Num castellers from colla */
    public function castellersCounter(): int
    {
        return $this->colla->castellers()->count();
    }

    /** Number members with telegram access from colla*/
    public function membersTelegramCounter(): int
    {
        return CastellerTelegram::query()->where('colla_id', $this->colla->getId())->count();
    }

    /** Num menbres awith web access from colla */
    public function membersWebCounter(): int
    {
        return CastellerConfig::leftJoin('castellers', 'casteller_config.casteller_id', '=', 'castellers.id_casteller')
            ->where('castellers.colla_id', $this->colla->getId())
            ->where('auth_token_enabled', '!=', 0)
            ->count();
    }

    /** Num notifications from colla */
    public function notificationsCounter(): int
    {
        return Notification::query()->where('colla_id', $this->colla->getId())->count();
    }
}
