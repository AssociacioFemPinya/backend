<?php

namespace App\Policies;

use App\Board;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BoardPolicy
{
    use HandlesAuthorization;

    /** Only get Tag own Colla
     * @return bool
     */
    public function getBoard(User $auth, Board $board)
    {
        return $auth->colla_id === $board->colla_id;
    }
}
