<?php

declare(strict_types=1);

namespace App\Factories;

use App\Period;
use Symfony\Component\HttpFoundation\ParameterBag;

class PeriodFactory
{
    public static function make(int $collaId, ParameterBag $bag): Period
    {
        $period = new Period();
        $period->setAttribute('colla_id', $collaId);

        return self::update($period, $bag);
    }

    public static function update(Period $period, ParameterBag $bag): Period
    {

        if ($bag->has('start_period')) {
            $period->setAttribute('start_period', $bag->get('start_period'));
        }

        if ($bag->has('end_period')) {
            $period->setAttribute('end_period', $bag->get('end_period'));
        }

        if ($bag->has('name')) {
            $period->setAttribute('name', $bag->get('name'));
        }

        return $period;
    }
}
