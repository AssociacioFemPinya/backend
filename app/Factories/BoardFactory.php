<?php

declare(strict_types=1);

namespace App\Factories;

use App\Board;
use Symfony\Component\HttpFoundation\ParameterBag;

final class BoardFactory
{
    public static function make(int $collaId, ParameterBag $bag): Board
    {
        $board = new Board();

        $board->setAttribute('colla_id', $collaId);

        return self::update($board, $bag);
    }

    public static function update(Board $board, ParameterBag $bag): Board
    {
        if ($bag->has('name')) {
            if ($name = $bag->get('name')) {
                $board->setAttribute('name', $name);
            }
        }

        if ($bag->has('type')) {
            if ($type = $bag->getAlpha('type')) {
                $board->setAttribute('type', $type);
            }
        }

        if ($bag->has('data')) {
            if ($data = $bag->get('data')) {
                $board->setAttribute('data', $data);
            }
        }

        if ($bag->has('data_code')) {
            if ($data_code = $bag->get('data_code')) {
                $board->setAttribute('data_code', $data_code);
            }
        }

        if ($bag->has('html_pinya')) {
            if ($html_pinya = $bag->get('html_pinya')) {
                $board->setAttribute('html_pinya', $html_pinya);
            }
        }

        if ($bag->has('html_folre')) {
            if ($html_folre = $bag->get('html_folre')) {
                $board->setAttribute('html_folre', $html_folre);
            }
        }

        if ($bag->has('html_manilles')) {
            if ($html_manilles = $bag->get('html_manilles')) {
                $board->setAttribute('html_manilles', $html_manilles);
            }
        }

        if ($bag->has('html_puntals')) {
            if ($html_puntals = $bag->get('html_puntals')) {
                $board->setAttribute('html_puntals', $html_puntals);
            }
        }

        return $board;
    }
}
