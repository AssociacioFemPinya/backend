<?php

declare(strict_types=1);

namespace App\Services;

use App\Attendance;
use App\Enums\AttendanceStatus;
use App\Services\Filters\CastellersFilter;
use App\Services\Filters\EventsFilter;
use Illuminate\Database\Eloquent\Builder;

class AttendanceStats
{
    private Builder $eloquentBuilder;

    private EventsFilter $eventsFilter;

    private CastellersFilter $castellersFilter;

    public function __construct(EventsFilter $eventsFilter, CastellersFilter $castellersFilter)
    {
        $this->eventsFilter = $eventsFilter;
        $this->castellersFilter = $castellersFilter;
    }

    public function getAttendancePercentage(): Builder
    {
        $num_events = $this->eventsFilter->eloquentBuilder()->count();

        return Attendance::query()
            ->selectRaw('castellers.id_casteller as casteller_id, sum(case when attendance.status = '.AttendanceStatus::YES." then 1 else 0 end)/$num_events as percentage")
            ->joinSub($this->eventsFilter->eloquentBuilder(), 'events', function ($join) {
                $join->on('attendance.event_id', '=', 'events.id_event');
            })
            ->rightJoinSub($this->castellersFilter->eloquentBuilder(), 'castellers', function ($join) {
                $join->on('attendance.casteller_id', '=', 'castellers.id_casteller');
            })
            ->groupBy('castellers.id_casteller');
    }
}
