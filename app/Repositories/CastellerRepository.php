<?php

declare(strict_types=1);

namespace App\Repositories;

use App\BoardEvent;
use App\Casteller;
use App\Enums\AttendanceStatus;
use App\Services\Filters\CastellersFilter;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

class CastellerRepository extends BaseRepository
{
    public function fetchFromTags(CastellersFilter $filter): Collection
    {
        return $filter->eloquentBuilder()->get();
    }

    public function fetchFromAttendance(CastellersFilter $filter, BoardEvent $boardEvent, array $attendanceStatus = [], array $attendanceStatusVerified = [], ?string $filterText = null): Collection
    {
        $castellers = $filter->eloquentBuilder();

        $castellers->leftJoin('attendance', function (JoinClause $leftJoin) use ($boardEvent) {
            $leftJoin->on('castellers.id_casteller', '=', 'attendance.casteller_id');
            $leftJoin->where(function ($query) use ($boardEvent) {
                $query->orWhere('attendance.event_id', $boardEvent->getEventId());
                $query->orWhereNull('attendance.casteller_id');
            });
        })
            ->with('attendance', function ($q) use ($boardEvent) {
                $q->where('event_id', $boardEvent->getEventId());
            })
            ->with('boardPosition', function ($q) use ($boardEvent) {
                $q->where('event_id', $boardEvent->getEventId());
                $q->where('board_id', $boardEvent->getBoardId());
                $q->where('board_event_id', $boardEvent->getId());
            });

        $castellers->where(function ($query) use ($attendanceStatusVerified) {
            if (in_array(AttendanceStatus::YES, $attendanceStatusVerified)) {
                $query->orWhere('attendance.status_verified', AttendanceStatus::YES);
            }

            if (in_array(AttendanceStatus::NO, $attendanceStatusVerified)) {
                $query->orWhere('attendance.status_verified', AttendanceStatus::NO);
            }

            if (in_array(AttendanceStatus::UNKNOWN, $attendanceStatusVerified)) {

                $query->orwhere(function ($q) {
                    $q->orWhereNull('attendance.status_verified');
                    $q->orWhere('attendance.status_verified', AttendanceStatus::UNKNOWN);
                });
            }
        });

        $castellers->where(function ($query) use ($attendanceStatus) {
            if (in_array(AttendanceStatus::YES, $attendanceStatus)) {
                $query->orWhere('attendance.status', AttendanceStatus::YES);
            }

            if (in_array(AttendanceStatus::NO, $attendanceStatus)) {
                $query->orWhere('attendance.status', AttendanceStatus::NO);
            }

            if (in_array(AttendanceStatus::UNKNOWN, $attendanceStatus)) {

                $query->orwhere(function ($q) {
                    $q->orWhereNull('attendance.status');
                    $q->orWhere('attendance.status', AttendanceStatus::UNKNOWN);
                });
            }
        });

        return $castellers->get();
    }

    public function save(Casteller $casteller): bool
    {
        return $casteller->save();
    }

    public function delete(Casteller $casteller): bool
    {
        return $casteller->delete();
    }

    public function fetchOneById(int $castellerId, array $with = []): ?Casteller
    {
        return Casteller::query()
            ->with($with)
            ->find($castellerId);
    }
}
