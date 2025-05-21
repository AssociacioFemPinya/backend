<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static EventTypeEnum Assaig()
 * @method static EventTypeEnum Actuacio()
 * @method static EventTypeEnum Activitat()
 */
class EventTypeEnum extends Enum
{
    public const ASSAIG = 1;

    public const ACTUACIO = 2;

    public const ACTIVITAT = 3;

    public static function getType(): array
    {
        return [self::Assaig()->value(), self::Actuacio()->value(), self::Activitat()->value()];
    }
}
