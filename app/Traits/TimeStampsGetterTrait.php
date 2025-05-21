<?php

declare(strict_types=1);

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

trait TimeStampsGetterTrait
{
    public function getCreatedAt(): Carbon
    {
        /** @var Model $this */
        return $this->getAttribute('created_at');
    }

    public function getUpdatedAt(): Carbon
    {
        /** @var Model $this */
        return $this->getAttribute('updated_at');
    }
}
