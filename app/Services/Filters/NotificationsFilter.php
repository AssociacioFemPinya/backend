<?php

declare(strict_types=1);

namespace App\Services\Filters;

use App\Casteller;
use App\Colla;
use App\Notification;

class NotificationsFilter extends BaseFilter
{
    public function __construct(Colla $colla)
    {
        parent::__construct($this->eloquentBuilder = Notification::query()
            ->where('notifications.colla_id', $colla->getId())
            ->select('notifications.*'));
    }

    /**
     * Filter by notifications that has been send to a Casteller
     *
     * @return NotificationsFilter
     */
    public function withNotifiedCasteller(Casteller $casteller)
    {
        $this->eloquentBuilder()->leftJoin(
            'notification_order',
            'notifications.id_notification',
            '=',
            'notification_order.notification_id'
        )->where('notification_order.casteller_id', '=', $casteller->getId())->select('notifications.*');

        return $this;
    }

    public function withTypes(array $types)
    {
        $this->eloquentBuilder()
            ->whereIn('type', $types);

        return $this;
    }

    public function visible(bool $visible = true)
    {
        $this->eloquentBuilder()
            ->where('visible', $visible);

        return $this;
    }
}
