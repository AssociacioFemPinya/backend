<?php

namespace App\Policies;

use App\Casteller;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CastellerPolicy
{
    use HandlesAuthorization;

    /** Only get Casteller own Colla
     * @return bool
     */
    public function getCasteller(User $auth, Casteller $casteller)
    {
        return $auth->getCollaId() === $casteller->getCollaId();
    }
}
