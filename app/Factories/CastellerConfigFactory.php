<?php

declare(strict_types=1);

namespace App\Factories;

use App\CastellerConfig;
use Faker\Factory;
use Symfony\Component\HttpFoundation\ParameterBag;

final class CastellerConfigFactory
{
    public static function make(int $castellerId, ParameterBag $bag): CastellerConfig
    {
        $faker = Factory::create();

        $castellerConfig = new CastellerConfig();

        $castellerConfig->setAttribute('casteller_id', $castellerId);

        $castellerConfig->setAttribute('telegram_token', $faker->regexify('[A-Za-z0-9]{8}'));

        $castellerConfig->setAttribute('api_token', $faker->regexify('[A-Za-z0-9]{32}'));

        return self::update($castellerConfig, $bag);
    }

    public static function update(CastellerConfig $castellerConfig, ParameterBag $bag): CastellerConfig
    {

        if ($bag->has('telegram_token')) {
            if ($telegram_token = $bag->get('telegram_token')) {
                $castellerConfig->setAttribute('telegram_token', $telegram_token);
            }
        }

        if ($bag->has('telegram_enabled')) {
            if ($telegram_enabled = $bag->get('telegram_enabled')) {
                $castellerConfig->setAttribute('telegram_enabled', $telegram_enabled);
            }
        }

        if ($bag->has('api_token')) {
            if ($api_token = $bag->get('api_token')) {
                $castellerConfig->setAttribute('api_token', $api_token);
            }
        }

        if ($bag->has('api_token_enabled')) {
            if ($api_token_enabled = $bag->get('api_token_enabled')) {
                $castellerConfig->setAttribute('api_token_enabled', $api_token_enabled);
            }
        }

        if ($bag->has('tecnica')) {
            if ($tecnica = $bag->get('tecnica')) {
                $castellerConfig->setAttribute('tecnica', $tecnica);
            }
        }

        if ($bag->has('auth_token_enabled')) {
            if ($auth_token_enabled = $bag->get('auth_token_enabled')) {
                $castellerConfig->setAttribute('auth_token_enabled', $auth_token_enabled);
            }
        }

        return $castellerConfig;
    }
}
