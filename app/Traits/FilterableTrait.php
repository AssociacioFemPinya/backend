<?php

declare(strict_types=1);

namespace App\Traits;

use App\Colla;

trait FilterableTrait
{
    public static function filter(Colla $colla)
    {
        return new static::$filterClass($colla);
    }
}
