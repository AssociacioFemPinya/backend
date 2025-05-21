<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static ScaledAttendanceStatus YesVerified()
 * @method static ScaledAttendanceStatus Yes()
 * @method static ScaledAttendanceStatus No()
 * @method static ScaledAttendanceStatus Unknown()
 */
class ScaledAttendanceStatus extends Enum
{
    public const YES = 1;

    public const NO = 2;

    public const UNKNOWN = 3;

    public const YESVERIFIED = 4;

    public static function getStatus(): array
    {
        return [self::Yes()->value(), self::No()->value(), self::Unknown()->value(), self::YesVerified()->value()];
    }
}
