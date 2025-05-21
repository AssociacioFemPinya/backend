<?php

declare(strict_types=1);

namespace App\Factories;

use App\Board;
use App\Row;
use Symfony\Component\HttpFoundation\ParameterBag;

final class RowFactory
{
    public static function make(Board $board, ParameterBag $bag): Row
    {
        $row = new Row();

        $row->setAttribute('colla_id', $board->getCollaId());
        $row->setAttribute('board_id', $board->getId());

        return self::update($row, $bag);
    }

    public static function update(Row $row, ParameterBag $bag): Row
    {
        if ($bag->has('div_id')) {
            if ($div_id = $bag->getInt('div_id')) {
                $row->setAttribute('div_id', $div_id);
            }
        }

        if ($bag->has('row')) {
            if ($rowName = $bag->get('row')) {
                $row->setAttribute('row', $rowName);
            }
        }

        if ($bag->has('cord')) {
            if ($cord = $bag->getInt('cord')) {
                $row->setAttribute('cord', $cord);
            }
        }

        if ($bag->has('side')) {
            if ($side = $bag->get('side')) {
                $row->setAttribute('side', $side);
            }
        }

        if ($bag->has('id_position')) {
            if ($pos = $bag->get('id_position')) {
                $row->setAttribute('id_position', $pos);
            }
        }

        if ($bag->has('position')) {
            if ($pos = $bag->get('position')) {
                $row->setAttribute('position', $pos);
            }
        }

        if ($bag->has('base')) {
            if ($base = $bag->getAlpha('base')) {
                $row->setAttribute('base', $base);
            }
        }

        return $row;
    }
}
