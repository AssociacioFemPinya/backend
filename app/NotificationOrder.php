<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Notification order is generated automatically when a NotificationReady event is raised.
 * One notification order represents the intention of notify a Casteller about something by any channels available.
 *
 * One notification order will generate the necessary notificationJobs.
 */
class NotificationOrder extends Model
{
    protected $table = 'notification_order';

    protected $primaryKey = 'id_notification_order';

    protected $fillable = ['casteller_id'];

    protected $events = [
        'created' => NotificationOrderListener::class,
    ];

    /** Get the casteller that will be notified by this notification order */
    public function getCasteller(): Casteller
    {
        return $this->casteller()->get()->first();
    }

    /** Get the notification that generated this notification_order */
    public function getNotification(): Notification
    {
        return $this->notification()->get()->first();
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'id_notification');
    }

    public function casteller(): BelongsTo
    {
        return $this->belongsTo(Casteller::class, 'casteller_id', 'id_casteller');
    }

    public function logs()
    {
        return $this->hasMany(NotificationLog::class, 'notification_order_id', 'id_notification_order');
    }
}
