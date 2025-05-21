<?php

declare(strict_types=1);

namespace App\Services\Filters;

use App\Board;
use App\BoardEvent;
use App\Colla;
use App\Event;
use App\Traits\DatatablesFilterTrait;
use Illuminate\Database\Eloquent\Builder;

class EventBoardsFilter extends BaseFilter
{
    private Builder $eloquentBuilder;

    use DatatablesFilterTrait;

    public function __construct(Colla $colla)
    {
        parent::__construct($this->eloquentBuilder = BoardEvent::query()->leftJoin(
            'events',
            'events.id_event',
            '=',
            'board_event.event_id'
        )
            ->where('events.colla_id', $colla->getId())
            ->select('board_event.*'));
    }

    public function favouritesByBoard(Board $board): self
    {
        $this->eloquentBuilder
            ->where('board_id', '=', $board->getId())
            ->where('favourite', 1);

        return $this;
    }

    public function notFavouritesByBoard(Board $board): self
    {
        $this->eloquentBuilder
            ->where('board_id', '=', $board->getId())
            ->where('favourite', 0);

        return $this;
    }

    public function excludeBoardEvents(BoardEvent|array $boardEvents): self
    {
        if (is_array($boardEvents)) {
            $this->eloquentBuilder
                ->whereNotIn('id', $boardEvents);
        } else {
            $this->eloquentBuilder
                ->where('id', '!=', $boardEvents->getId());
        }

        return $this;

    }

    public function inEvent(Event $event): self
    {
        $this->eloquentBuilder
            ->where('events.id_event', $event->getId());

        return $this;
    }

    public function usingBoard(Board $board): self
    {
        $this->eloquentBuilder
            ->where('board_event.board_id', $board->getId());

        return $this;
    }

    public function orderByEventDate(): self
    {
        $this->eloquentBuilder
            ->orderBy('events.start_date', 'desc');

        return $this;
    }

    public function orderByBoardEventName(): self
    {
        $this->eloquentBuilder
            ->orderBy('board_event.name', 'asc');

        return $this;
    }
}
