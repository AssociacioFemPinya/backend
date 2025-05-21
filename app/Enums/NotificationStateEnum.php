<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static NotificationStateEnum Pending()
 * @method static NotificationStateEnum Sent()
 * @method static NotificationStateEnum Failed()
 */
class NotificationStateEnum extends Enum
{
    public const PENDING = 1;

    public const SENT = 2;

    public const FAILED = 3;

    public static function getType(): array
    {
        return [self::Pending()->value(), self::Sent()->value(), self::Failed()->value()];
    }
}
