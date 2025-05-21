<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Attendance;

class AttendanceRepository
{
    public function attenders(int $eventId): array
    {
        $attenders['YES'] = Attendance::query()->where('id_event', $eventId)->where('status', 'YES')->get();
        $attenders['YES_VERIFIED'] = Attendance::query()->where('id_event', $eventId)->where('status_verified', 'YES')->get();
        $attenders['NO'] = Attendance::query()->where('id_event', $eventId)->where('status', 'NO')->get();
        $attenders['NO_VERIFIED'] = Attendance::query()->where('id_event', $eventId)->where('status_verified', 'NO')->get();

        return $attenders;
    }

    public function fetchAttendanceCastellerEvent(int $castellerId, int $eventId): ?Attendance
    {
        return Attendance::query()
            ->where('casteller_id', $castellerId)
            ->where('event_id', $eventId)
            ->first();
    }

    public function save(Attendance $attendance): bool
    {
        return $attendance->save();
    }

    public function delete(Attendance $attendance): bool
    {
        return $attendance->delete();
    }
}
