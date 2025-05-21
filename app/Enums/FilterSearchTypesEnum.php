<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static FilterMembersEnum And()
 * @method static FilterMembersEnum Or()
 */
class FilterSearchTypesEnum extends Enum
{
    public const AND = 'AND';

    public const OR = 'OR';

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
}
