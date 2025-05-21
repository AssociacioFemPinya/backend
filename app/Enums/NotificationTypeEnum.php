<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static NotificationTypeEnum Message()
 * @method static NotificationTypeEnum Reminder()
 * @method static NotificationTypeEnum Event()
 * @method static NotificationTypeEnum Scheduled_Message()
 */
class NotificationTypeEnum extends Enum
{
    public const MESSAGE = 1;

    public const REMINDER = 2;

    public const EVENT = 3;

    public const SCHEDULED_MESSAGE = 4;

    public static function getType(): array
    {
        return [self::Message()->value(), self::Reminder()->value(), self::Event()->value(), self::Scheduled_Message()->value()];
    }
}
