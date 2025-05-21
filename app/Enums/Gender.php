<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static Gender Female()
 * @method static Gender Male()
 * @method static Gender Nobinary()
 * @method static Gender Noanswer()
 */
class Gender extends Enum
{
    public const FEMALE = 0;

    public const MALE = 1;

    public const NOBINARY = 2;

    public const NOANSWER = 3;

    public static function getTypes(): array
    {
        return [self::Female()->value(), self::Male()->value(), self::Nobinary()->value(), self::Noanswer()->value()];
    }
}
