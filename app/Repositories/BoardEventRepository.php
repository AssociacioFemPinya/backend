<?php

declare(strict_types=1);

namespace App\Repositories;

use App\BoardEvent;
use App\BoardPosition;
use App\Casteller;
use App\Factories\BoardPositionFactory;
use App\Row;

final class BoardEventRepository extends BaseRepository
{
    public function createBoardPosition(BoardEvent $boardEvent, Casteller $casteller, Row $row): BoardPosition
    {
        return BoardPositionFactory::make($boardEvent, $casteller, $row);
    }

    public function updateBoardPosition(BoardPosition $boardPosition, Casteller $casteller, Row $row): BoardPosition
    {
        return BoardPositionFactory::update($boardPosition, $casteller, $row);
    }

    public function saveBoardPosition(BoardPosition $boardPosition): bool
    {
        return $boardPosition->save();
    }

    public function saveRow(Row $row): bool
    {
        return $row->save();
    }

    public function fetchBoardPositionFromCastellerInBoardEvent(BoardEvent $boardEvent, Casteller $casteller): ?BoardPosition
    {
        return BoardPosition::query()
            ->where('board_event_id', $boardEvent->getId())
            ->where('casteller_id', $casteller->getId())
            ->first();
    }

    public function fetchBoardPositionFromRowInBoardEvent(BoardEvent $boardEvent, Row $row): ?BoardPosition
    {
        return BoardPosition::query()
            ->where('board_event_id', $boardEvent->getId())
            ->where('row_id', $row->getId())
            ->first();
    }

    public function updateAllBoardEventByCollaId(int $collaId, array $update): void
    {
        BoardEvent::query()
            ->select('board_event.*')
            ->join('boards', function ($join) {
                $join->on('board_event.board_id', '=', 'boards.id_board');

            })
            ->where('boards.colla_id', $collaId)
            ->update($update);
    }

    public function fetchOneById(int $id, array $with = []): ?BoardEvent
    {
        return BoardEvent::query()
            ->with($with)
            ->find($id);
    }

    public function fetchDisplayBoardEventByCollaId(int $collaId): ?BoardEvent
    {
        return BoardEvent::query()
            ->select('board_event.*')
            ->join('boards', function ($join) {
                $join->on('board_event.board_id', '=', 'boards.id_board');
                $join->where('display', true);
            })
            ->where('boards.colla_id', $collaId)
            ->first();
    }
}
