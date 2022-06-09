<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Doctrine\DbalType;

use Throwable;
use DateTimeZone;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class DateTimeZoneType extends StringType
{
	public const NAME = 'datetime_zone';

	/**
	 * {@inheritDoc}
	 */
	public function getName(): string
	{
		return self::NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTimeZone
	{
		if (NULL === $value || $value instanceof DateTimeZone) {
			return $value;
		}

		try {
			$dateTimeZone = new DateTimeZone($value);
		} catch (Throwable $e) {
			throw ConversionException::conversionFailedFormat(
				$value,
				$this->getName(),
				'valid timezone name'
			);
		}

		return $dateTimeZone;
	}

	/**
	 * {@inheritdoc}
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if (NULL === $value) {
			return NULL;
		}

		if ($value instanceof DateTimeZone) {
			return $value->getName();
		}

		throw ConversionException::conversionFailedInvalidType(
			$value,
			$this->getName(),
			['null', DateTimeZone::class]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return TRUE;
	}
}
