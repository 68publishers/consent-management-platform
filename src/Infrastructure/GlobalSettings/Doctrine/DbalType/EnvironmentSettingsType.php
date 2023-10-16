<?php

declare(strict_types=1);

namespace App\Infrastructure\GlobalSettings\Doctrine\DbalType;

use App\Domain\GlobalSettings\ValueObject\EnvironmentSettings;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;

final class EnvironmentSettingsType extends JsonType
{
    public function getName(): string
    {
        return EnvironmentSettings::class;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?EnvironmentSettings
    {
        $value = parent::convertToPHPValue($value, $platform);

        return null !== $value ? EnvironmentSettings::fromSafeNative($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null !== $value && !($value instanceof EnvironmentSettings)) {
            throw ConversionException::conversionFailed($value, EnvironmentSettings::class);
        }

        return parent::convertToDatabaseValue($value?->toNative(), $platform);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
