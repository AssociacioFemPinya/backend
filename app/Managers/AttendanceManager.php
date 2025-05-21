<?php

declare(strict_types=1);

namespace App\Managers;

use App\Attendance;
use App\Factories\AttendanceFactory;
use App\Repositories\AttendanceRepository;
use Symfony\Component\HttpFoundation\ParameterBag;

class AttendanceManager
{
    private AttendanceRepository $repository;

    public function __construct(AttendanceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function attenders(int $eventId): array
    {
        return $this->repository->attenders($eventId);
    }

    public function fetchAttendanceCastellerEvent(int $castellerId, int $eventId): ?Attendance
    {
        return $this->repository->fetchAttendanceCastellerEvent($castellerId, $eventId);
    }

    public function createAttendance(int $idCasteller, int $idEvent, ParameterBag $bag): Attendance
    {
        $attendance = AttendanceFactory::make($idCasteller, $idEvent, $bag);
        $this->repository->save($attendance);

        return $attendance;
    }

    public function updateAttendance(Attendance $attendance, ParameterBag $bag): Attendance
    {
        $attendance = AttendanceFactory::update($attendance, $bag);
        $this->repository->save($attendance);

        return $attendance;
    }

    public function deleteAttendance(Attendance $attendance)
    {
        $this->repository->delete($attendance);

    }

    public function save(Attendance $attendance): bool
    {
        return $this->repository->save($attendance);
    }
}
