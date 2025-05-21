<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static EventTypeEnum Assaig()
 * @method static EventTypeEnum Actuacio()
 * @method static EventTypeEnum Activitat()
 */
class EventTypeNameEnum extends Enum
{
    public const ASSAIG = 'assaig';

    public const ACTUACIO = 'actuacio';

    public const ACTIVITAT = 'activitat';

    public static function getType(): array
    {
        return [self::Assaig()->value(), self::Actuacio()->value(), self::Activitat()->value()];
    }
}
