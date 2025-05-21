<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static TypeTags Icon()
 * @method static TypeTags Text()
 */
class TelegramInteractionType extends Enum
{
    public const ICON = 1;

    public const TEXT = 2;

    public static function getTypes(): array
    {
        return [self::Icon()->value(), self::Text()->value()];
    }
}
