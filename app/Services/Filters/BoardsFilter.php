<?php

declare(strict_types=1);

namespace App\Services\Filters;

use App\Colla;
use App\Board;

class BoardsFilter extends BaseFilter
{
    public function __construct(Colla $colla)
    {
        parent::__construct($this->eloquentBuilder = Board::query()
            ->where('boards.colla_id', $colla->getId())
            ->select('boards.*'));
    }

    public function visible(bool $visible = true)
    {
        $this->eloquentBuilder()
            ->where('visible', $visible);

        return $this;
    }
}
