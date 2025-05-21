<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Board;
use App\Row;
use Illuminate\Support\Collection;

class BoardRepository extends BaseRepository
{
    public function fetchOneById(int $boardId, array $with = []): ?Board
    {
        return Board::query()
            ->with($with)
            ->find($boardId);
    }

    public function fetchRowInBoardByBaseDivId(int $boardId, int $divId, string $base): ?Row
    {
        return Row::query()
            ->where('board_id', $boardId)
            ->where('div_id', $divId)
            ->where('base', $base)
            ->first();
    }

    public function fetchDisplayBoardByCollaId(int $collaId, array $with = []): ?Board
    {
        return Board::query()
            ->select('boards.*')
            ->with($with)
            ->join('board_event', function ($join) {
                $join->on('boards.id_board', '=', 'board_event.board_id');
                $join->where('display', true);
            })
            ->where('boards.colla_id', $collaId)
            ->first();
    }

    public function saveBoard(Board $board): bool
    {
        return $board->save();
    }

    public function fetchAllBoardsByCollaId(int $collaId, array $with = []): Collection
    {
        return Board::query()
            ->with($with)
            ->where('colla_id', $collaId)
            ->get();
    }
}
