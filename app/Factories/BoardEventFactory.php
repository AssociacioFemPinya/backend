<?php

declare(strict_types=1);

namespace App\Factories;

use App\BoardEvent;
use Symfony\Component\HttpFoundation\ParameterBag;

class BoardEventFactory
{
    public static function make(ParameterBag $bag, int $eventId, int $boardId): BoardEvent
    {
        $attendance = new BoardEvent();

        $attendance->setAttribute('event_id', $eventId);
        $attendance->setAttribute('board_id', $boardId);

        return self::update($attendance, $bag);
    }

    public static function update(BoardEvent $boardEvent, ParameterBag $bag): BoardEvent
    {
        if ($bag->has('display')) {
            if ($display = $bag->getBoolean('display')) {
                $boardEvent->setAttribute('display', $display);
            } else {
                $boardEvent->setAttribute('display', false);
            }
        }

        if ($bag->has('favourite')) {
            if ($favourite = $bag->getBoolean('favourite')) {
                $boardEvent->setAttribute('favourite', $favourite);
            } else {
                $boardEvent->setAttribute('favourite', false);
            }
        }

        if ($bag->has('name')) {
            $boardEvent->setAttribute('name', $bag->get('name'));
        }

        return $boardEvent;
    }
}
