<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static CastellersStatusEnum Noob()
 * @method static CastellersStatusEnum Active()
 * @method static CastellersStatusEnum Inactive()
 * @method static CastellersStatusEnum Injured()
 * @method static CastellersStatusEnum ActivePinya()
 * @method static CastellersStatusEnum ActiveAll()
 * @method static CastellersStatusEnum All()
 */
class CastellersStatusEnum extends Enum
{
    public const NOOB = 1;

    public const ACTIVE = 2;

    public const INACTIVE = 3;

    public const INJURED = 4;

    public const ACTIVE_PINYA = 5;

    public const ACTIVE_ALL = 6;

    public const ALL = 7;

    public static function getStatus(): array
    {
        return [self::Noob()->value(), self::Active()->value(), self::Inactive()->value(), self::Injured()->value(), self::ActivePinya()->value(), self::ActiveAll()->value()];
    }

    public static function getBy($value)
    {
        return match ($value) {
            '1' => self::Noob(),
            '2' => self::Active(),
            '3' => self::Inactive(),
            '4' => self::Injured(),
            '5' => self::ActivePinya(),
            '6' => self::ActiveAll(),
            '7' => self::All(),
        };
    }
}
