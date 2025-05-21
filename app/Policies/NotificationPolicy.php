<?php

namespace App\Policies;

use App\Notification;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationPolicy
{
    use HandlesAuthorization;

    /** Only get Tag own Colla
     * @return bool
     */
    public function getNotification(User $auth, Notification $notification)
    {
        return $auth->colla_id === $notification->colla_id;
    }
}
