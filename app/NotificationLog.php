<?php

declare(strict_types=1);

namespace App;

use App\Enums\NotificationStateEnum;
use App\Helpers\DateHelper;
use App\Traits\FilterableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * NotificationLog stores the different states (NotificationStateEnum) that a NotificationJob goes through.
 */
class NotificationLog extends Model
{
    use FilterableTrait;

    protected $table = 'notification_log';

    protected $fillable = ['status', 'channel'];

    protected static $filterClass = \App\Services\Filters\NotificationLogsFilter::class;

    public function getStatus(): int
    {
        return $this->getAttribute('status');
    }

    public function getStatusStr(): string
    {
        return NotificationStateEnum::getById($this->getStatus());
    }

    /** Get the creation time date */
    public function getCreatedAt(): string
    {
        return DateHelper::dateTimeToCurrentTimezone($this->getAttribute('created_at')->toDateTimeString());
    }

    /** Returns the notification that generated this log */
    public function getNotification(): Notification
    {
        return $this->notification_order->notification;
    }

    /** Returns the casteller notified by the job that generated this log */
    public function getCasteller(): Casteller
    {
        return $this->notification_order->casteller;
    }

    public function notification_order(): BelongsTo
    {
        return $this->belongsTo(NotificationOrder::class, 'notification_order_id', 'id_notification_order');
    }
}
