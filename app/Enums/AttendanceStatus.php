<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static AttendanceStatus Yes()
 * @method static AttendanceStatus No()
 * @method static AttendanceStatus Unknown()
 */
class AttendanceStatus extends Enum
{
    public const YES = 1;

    public const NO = 2;

    public const UNKNOWN = 3;

    public static function getStatus(): array
    {
        return [self::Yes()->value(), self::No()->value(), self::Unknown()->value()];
    }
}
