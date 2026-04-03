<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static FilterSearchTypesEnum And()
 * @method static FilterSearchTypesEnum Or()
 * @method static FilterSearchTypesEnum Except()
 */
class FilterSearchTypesEnum extends Enum
{
    public const AND = 'AND';

    public const OR = 'OR';

    public const EXCEPT = 'EXCEPT';

    public static function validOrAnd(string $searchTypes): string
    {
        return (FilterSearchTypesEnum::isValid($searchTypes))
            ? $searchTypes
            : self::AND;
    }

    public static function validOrOr(string $searchTypes): string
    {
        return (FilterSearchTypesEnum::isValid($searchTypes))
            ? $searchTypes
            : self::OR;
    }

    public static function validOrExcept(string $searchTypes): string
    {
        return (FilterSearchTypesEnum::isValid($searchTypes))
            ? $searchTypes
            : self::EXCEPT;
    }
}
