<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static TypeNationalId Dni()
 * @method static TypeNationalId Nie()
 * @method static TypeNationalId Passport()
 */
class TypeNationalId extends Enum
{
    public const DNI = 'dni';

    public const NIE = 'nie';

    public const PASSPORT = 'passport';

    public static function getTypes()
    {
        return [self::Dni()->value(), self::Nie()->value(), self::Passport()->value()];
    }
}
