<?php

namespace App\Policies;

use App\Multievent;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MultieventPolicy
{
    use HandlesAuthorization;

    public function getEvent(User $auth, Multievent $multievent)
    {
        return $auth->getCollaId() === $multievent->getCollaId();
    }
}
