<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static Lang Ca()
 * @method static Lang Es()
 * @method static Lang En()
 * @method static Lang Fr()
 */
class Lang extends Enum
{
    //Please follow ISO 639-1 Language Codes for new languages
    public const CA = 'CAT';

    public const ES = 'ESP';

    public const EN = 'ENG';

    public const FR = 'FRA';

    public static function getTypes(): array
    {
        return [self::Ca()->value(), self::Es()->value(), self::En()->value(), self::Fr()->value()];
    }

    public static function getLangKey(string $lang): string
    {
        switch ($lang) {
            case 'CAT':
                return 'ca';
            case 'ESP':
                return 'es';
            case 'ENG':
                return 'en';
            case 'FRA':
                return 'fr';
        }
    }
}
