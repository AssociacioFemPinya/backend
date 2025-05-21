<?php

declare(strict_types=1);

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Generates Carbon datetime from date, hours and minutes
     */
    public static function parseDateTime(string $date, string $hour, string $minute): Carbon
    {
        // We should store dates in database in UTC, but after convert them from the expected timezone
        $user_timezone = env('USER_TIMEZONE', 'UTC');

        return Carbon::createFromFormat('d/m/Y\TH:i:s', $date.'T'.($hour ?? '00').':'.($minute ?? '00').':00', $user_timezone)->setTimeZone('UTC');
    }

    public static function isDateInPast(string $date, string $hour, string $minute): bool
    {
        $date = self::parseDateTime($date, $hour, $minute);
        $currentDateTime = Carbon::now();

        return $date->lessThan($currentDateTime);
    }

    public static function dateTimeToCurrentTimezone(string $dateTime): string
    {
        $user_timezone = env('USER_TIMEZONE', 'UTC');

        return Carbon::parse($dateTime)->timeZone($user_timezone)->toDateTimeString();
    }
}
