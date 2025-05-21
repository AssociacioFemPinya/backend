<?php

declare(strict_types=1);

namespace App\Factories;

use App\Ronda;
use Symfony\Component\HttpFoundation\ParameterBag;

class RondaFactory
{
    public static function make(ParameterBag $bag, int $eventId, int $boardEventId): Ronda
    {
        $attendance = new Ronda();

        $attendance->setAttribute('event_id', $eventId);
        $attendance->setAttribute('board_event_id', $boardEventId);

        return self::update($attendance, $bag);
    }

    public static function update(Ronda $ronda, ParameterBag $bag): Ronda
    {

        if ($bag->has('ronda')) {
            $ronda->setAttribute('ronda', $bag->getInt('ronda'));
        }

        return $ronda;
    }
}
