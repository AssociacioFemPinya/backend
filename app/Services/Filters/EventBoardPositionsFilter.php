<?php

declare(strict_types=1);

namespace App\Services\Filters;

use App\BoardEvent;
use App\BoardPosition;
use App\Colla;
use App\Event;
use App\Traits\DatatablesFilterTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EventBoardPositionsFilter extends BaseFilter
{
    private Builder $eloquentBuilder;

    use DatatablesFilterTrait;

    public function __construct(Colla $colla)
    {
        parent::__construct($this->eloquentBuilder = BoardPosition::query()
            ->leftJoin(
                'board_event',
                'board_event.id',
                '=',
                'board_position.board_event_id'
            )
            ->leftJoin(
                'events',
                'events.id_event',
                '=',
                'board_event.event_id'
            )
            ->where('events.colla_id', $colla->getId())
            ->select('board_position.*'));
    }

    public function getRowInfo(): Collection
    {
        return
        $this->eloquentBuilder
            ->select('casteller_id', 'row_id')
            ->with(['casteller:id_casteller,name,last_name,alias,height,status', 'row:id,div_id'])
            ->get();
    }

    public function fromCastellers(array $castellers): self
    {
        $this->eloquentBuilder
            ->whereIn('board_position.casteller_id', $castellers);

        return $this;
    }

    public function inBoardEvent(BoardEvent $boardEvent): self
    {
        $this->eloquentBuilder
            ->where('board_position.board_event_id', '=', $boardEvent->getId());

        return $this;
    }

    public function inEvent(Event $event): self
    {
        $this->eloquentBuilder
            ->where('board_position.event_id', '=', $event->getId());

        return $this;
    }

    public function inBase(string $base = BasesEnum::PINYA): self
    {
        $this->eloquentBuilder
            ->where('board_position.base', '=', $base);

        return $this;
    }
}
