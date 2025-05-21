<?php

namespace App\Policies;

use App\Event;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function getEvent(User $auth, Event $event)
    {
        return $auth->getCollaId() === $event->getCollaId();
    }
}
