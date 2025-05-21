<?php

namespace App\Policies;

use App\ScheduledNotification;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ScheduledNotificationPolicy
{
    use HandlesAuthorization;

    /** Only get Tag own Colla
     * @param  ScheduledNotification  $notification
     * @return bool
     */
    public function getScheduledNotification(User $auth, ScheduledNotification $scheduledNotification)
    {
        return $auth->colla_id === $scheduledNotification->colla_id;
    }
}
