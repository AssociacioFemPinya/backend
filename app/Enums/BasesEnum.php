<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static BasesEnum Pinya()
 * @method static BasesEnum Folre()
 * @method static BasesEnum Manilles()
 * @method static BasesEnum Puntals()
 */
class BasesEnum extends Enum
{
    public const PINYA = 'PINYA';

    public const FOLRE = 'FOLRE';

    public const MANILLES = 'MANILLES';

    public const PUNTALS = 'PUNTALS';

    public static function getTypes(): array
    {
        return [self::Pinya()->value(), self::Folre()->value(), self::Manilles()->value(), self::Puntals()->value()];
    }
}
