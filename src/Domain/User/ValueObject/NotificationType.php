<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractEnumValueObject;

final class NotificationType extends AbstractEnumValueObject
{
    public const CONSENT_DECREASED = 'consent_decreased';
    public const WEEKLY_OVERVIEW = 'weekly_overview';
    public const COOKIE_SUGGESTIONS = 'cookie_suggestions';

    public static function values(): array
    {
        return [
            self::CONSENT_DECREASED,
            self::WEEKLY_OVERVIEW,
            self::COOKIE_SUGGESTIONS,
        ];
    }
}
