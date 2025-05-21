<?php

declare(strict_types=1);

namespace App\Services\Filters;

use App\Casteller;
use App\Colla;
use App\ScheduledNotification;
use App\Traits\DatatablesFilterTrait;
use Illuminate\Database\Eloquent\Builder;

class ScheduledNotificationsFilter extends BaseFilter
{
    private Builder $eloquentBuilder;

    use DatatablesFilterTrait;

    public function __construct(Colla $colla)
    {
        parent::__construct($this->eloquentBuilder = ScheduledNotification::query()
            ->where('scheduled_notifications.colla_id', $colla->getId())
            ->select('scheduled_notifications.*'));
    }

    /**
     * Filter by scheduled notifications that has been send to a Casteller
     *
     * @return ScheduledNotificationsFilter
     */
    public function withNotifiedCasteller(Casteller $casteller)
    {
        $this->eloquentBuilder->leftJoin(
            'scheduled_notification_order',
            'scheduled_notifications.id_scheduled_notification',
            '=',
            'scheduled_notification_order.scheduled_notification_id'
        )->where('scheduled_notification_order.casteller_id', '=', $casteller->getId())->select('scheduled_notifications.*');

        return $this;
    }
}
