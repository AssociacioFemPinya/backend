<?php

declare(strict_types=1);

namespace App\Services\Filters;

use App\Attendance;
use App\Colla;
use App\Event;
use App\Traits\DatatablesFilterTrait;
use Illuminate\Database\Eloquent\Builder;

class EventAttendanceFilter extends BaseFilter
{
    private Builder $eloquentBuilder;

    use DatatablesFilterTrait;

    public function __construct(Colla $colla)
    {
        parent::__construct($this->eloquentBuilder =
            Attendance::query()
                ->leftJoin(
                    'events',
                    'events.id_event',
                    '=',
                    'attendance.event_id'
                )
                ->leftJoin(
                    'castellers',
                    'castellers.id_casteller',
                    '=',
                    'attendance.casteller_id'
                )
                ->where('events.colla_id', $colla->getId())
                ->select('attendance.*')
        );
    }

    public function getStatusAndAlias(): array
    {
        return
        $this->eloquentBuilder
            ->select('attendance.status', 'castellers.alias')
            ->orderBy('attendance.status', 'asc')
            ->orderBy('castellers.alias', 'asc')
            ->get()
            ->toArray();

    }

    public function fromEvent(Event $event): self
    {
        $this->eloquentBuilder
            ->where('events.id_event', $event->getId());

        return $this;
    }
}
