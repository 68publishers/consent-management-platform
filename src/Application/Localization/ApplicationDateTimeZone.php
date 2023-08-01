<?php

declare(strict_types=1);

namespace App\Application\Localization;

use DateTimeZone;

final class ApplicationDateTimeZone
{
    private static ?DateTimeZone $dateTimeZone = null;

    private function __construct() {}

    public static function set(DateTimeZone $dateTimeZone): void
    {
        self::$dateTimeZone = $dateTimeZone;
    }

    public static function get(): DateTimeZone
    {
        if (null === self::$dateTimeZone) {
            self::set(new DateTimeZone('UTC'));
        }

        return self::$dateTimeZone;
    }

    /**
     * @return string[]
     */
    public static function all(): array
    {
        return DateTimeZone::listIdentifiers();
    }
}
