<?php

declare(strict_types=1);

namespace App\Factories;

use App\Attendance;
use Symfony\Component\HttpFoundation\ParameterBag;

class AttendanceFactory
{
    public static function make(int $castellerId, int $eventId, ParameterBag $bag): Attendance
    {
        $attendance = new Attendance();

        $attendance->setAttribute('casteller_id', $castellerId);
        $attendance->setAttribute('event_id', $eventId);

        return self::update($attendance, $bag);
    }

    public static function update(Attendance $attendance, ParameterBag $bag): Attendance
    {

        if ($bag->has('id_attendance_external')) {
            $attendance->setAttribute('id_attendance_external', $bag->getInt('id_attendance_external'));
        }

        if ($bag->has('status')) {
            if ($status = $bag->getInt('status')) {
                $attendance->setAttribute('status', $status);
            }
        }

        if ($bag->has('status_verified')) {
            if ($statusVerified = $bag->getInt('status_verified')) {
                $attendance->setAttribute('status_verified', $statusVerified);
            }
        }

        if ($bag->has('companions')) {
            $companions = $bag->getInt('companions');
            if ($companions >= 0) {
                $attendance->setAttribute('companions', $companions);
            }
        }

        if ($bag->has('source')) {
            if ($source = $bag->get('source')) {
                $attendance->setAttribute('source', $source);
            }
        }

        if ($bag->has('options')) {
            if ($options = $bag->get('options')) {
                $attendance->setAttribute('options', $options);
            }
        }

        if ($bag->has('comments')) {
            if ($comments = $bag->get('comments')) {
                $attendance->setAttribute('comments', $comments);
            }
        }

        return $attendance;
    }
}
