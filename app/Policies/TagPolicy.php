<?php

namespace App\Policies;

use App\Tag;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
{
    use HandlesAuthorization;

    /** Only get Tag own Colla
     * @return bool
     */
    public function getTag(User $auth, Tag $tag)
    {
        return $auth->colla_id === $tag->colla_id;
    }
}
