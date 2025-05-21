<?php

namespace App\Services;

use App\Enums\EventTypeEnum;
use App\Event;
use Base32\Base32;
use OTPHP\TOTP;

class TOTPService
{
    /**
     * TOTP digest algorithm
     */
    public const DIGEST = 'sha1';

    /**
     * TOTP digits length
     */
    public const DIGITS = 6;

    /**
     * TOTP key length in bytes
     */
    public const KEY_LENGTH = 20;

    /**
     * Generates a TOTP instance for a specific event
     * If the period is 0, returns null as we'll use a static code instead
     *
     * @return TOTP|null
     */
    public static function generateTOTP(Event $event)
    {
        if (self::getPeriod($event) === 0) {
            return null;
        }

        $totp = TOTP::create(
            self::getSecretKey($event),
            self::getPeriod($event),
            self::DIGEST,
            self::DIGITS
        );

        return $totp;
    }

    /**
     * Generates a secret key for the TOTP based on the event ID
     *
     * @return string
     */
    private static function getSecretKey(Event $event)
    {
        $baseKey = config('app.key').$event->getId();
        $rawKey = hash('sha256', $baseKey, true);

        $truncatedKey = substr($rawKey, 0, self::KEY_LENGTH);

        return Base32::encode($truncatedKey);
    }

    /**
     * Generates a static code based on the event for period=0 scenario
     *
     * @return string
     */
    private static function getStaticCode(Event $event)
    {
        $baseCode = hash('sha256', config('app.key').$event->getId());
        $numericOnly = preg_replace('/[^0-9]/', '', $baseCode);

        return substr($numericOnly, 0, self::DIGITS);
    }

    /**
     * Generates a TOTP code for the current time
     * If period is 0, returns a static code instead
     *
     * @return string
     */
    public static function getCurrentCode(Event $event)
    {
        if (self::getPeriod($event) === 0) {
            return self::getStaticCode($event);
        }

        $totp = self::generateTOTP($event);

        return $totp->now();
    }

    /**
     * Verifies a TOTP code
     * If period is 0, verifies against the static code
     *
     * @return bool
     */
    public static function verifyCode(Event $event, string $code)
    {
        if (self::getPeriod($event) === 0) {

            return $code === self::getStaticCode($event);
        }

        $totp = self::generateTOTP($event);

        return $totp->verify($code);
    }

    /**
     * Calculates the remaining seconds until the next TOTP code change
     * If the period is 0, the code never expires (returns null)
     */
    public static function getRemainingSeconds($event): ?int
    {
        $period = self::getPeriod($event);

        if ($period === 0) {
            return null;
        }

        return $period - (time() % $period);
    }

    /**
     * Retrieves the TOTP period in seconds from the CollaConfig.
     */
    public static function getPeriod(Event $event): int
    {
        $colla = $event->getColla();
        $config = $colla->getConfig();

        if ($event->getType() === EventTypeEnum::ACTUACIO) {
            return 0;
        }

        return $config->getTOTPTokenExpiration();
    }
}
