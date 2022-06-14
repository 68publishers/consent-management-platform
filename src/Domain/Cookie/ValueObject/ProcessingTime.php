<?php

declare(strict_types=1);

namespace App\Domain\Cookie\ValueObject;

use App\Application\Helper\Estimate;
use App\Domain\Cookie\Exception\InvalidProcessingTimeException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractStringValueObject;

final class ProcessingTime extends AbstractStringValueObject
{
	public const PERSISTENT = 'persistent';
	public const SESSION = 'session';

	/**
	 * @param string $value
	 *
	 * @return static
	 * @throws \App\Domain\Cookie\Exception\InvalidProcessingTimeException
	 */
	public static function withValidation(string $value): self
	{
		if (self::PERSISTENT !== $value && self::SESSION !== $value && !Estimate::isMaskValid($value)) {
			throw InvalidProcessingTimeException::invalidValue($value);
		}

		return self::fromValue($value);
	}

	/**
	 * @param string      $locale
	 * @param string|NULL $fallbackLocale
	 *
	 * @return string
	 */
	public function print(string $locale, ?string $fallbackLocale = NULL): string
	{
		$value = $this->value();

		if (self::PERSISTENT === $value || self::SESSION === $value) {
			return $value;
		}

		return Estimate::fromMask($value, $locale, $fallbackLocale);
	}
}
