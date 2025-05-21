<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Tag;

class TagRepository
{
    public function save(Tag $tag): bool
    {
        return $tag->save();
    }

    public function delete(Tag $tag): bool
    {
        return $tag->delete();
    }
}
