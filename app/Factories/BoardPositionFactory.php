<?php

declare(strict_types=1);

namespace App\Factories;

use App\BoardEvent;
use App\BoardPosition;
use App\Casteller;
use App\Row;

final class BoardPositionFactory
{
    public static function make(BoardEvent $boardEvent, Casteller $casteller, Row $row): BoardPosition
    {
        $boardPosition = new BoardPosition();

        $boardPosition->setAttribute('board_id', $boardEvent->getBoardId());
        $boardPosition->setAttribute('event_id', $boardEvent->getEventId());
        $boardPosition->setAttribute('board_event_id', $boardEvent->getId());
        $boardPosition->setAttribute('colla_id', $casteller->getCollaId());

        return self::update($boardPosition, $casteller, $row);
    }

    public static function update(BoardPosition $boardPosition, Casteller $casteller, Row $row): BoardPosition
    {
        $boardPosition->setAttribute('casteller_id', $casteller->getId());
        $boardPosition->setAttribute('row_id', $row->getId());
        $boardPosition->setAttribute('base', $row->getBase());

        return $boardPosition;
    }
}
