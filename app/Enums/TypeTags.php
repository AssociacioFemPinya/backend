<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @method static TypeTags Castellers()
 * @method static TypeTags Events()
 * @method static TypeTags Positions()
 * @method static TypeTags Boards()
 */
class TypeTags extends Enum
{
    public const CASTELLERS = 'CASTELLERS';

    public const EVENTS = 'EVENTS';

    public const BOARDS = 'BOARDS';

    public const POSITIONS = 'POSITIONS';
}
