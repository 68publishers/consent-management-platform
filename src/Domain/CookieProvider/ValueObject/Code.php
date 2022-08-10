<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\ValueObject;

use App\Domain\CookieProvider\Exception\InvalidCodeException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractStringValueObject;

final class Code extends AbstractStringValueObject
{
	public const MAX_LENGTH = 70;

	/**
	 * @param string $code
	 *
	 * @return static
	 */
	public static function withValidation(string $code): self
	{
		if (self::MAX_LENGTH < mb_strlen($code)) {
			throw InvalidCodeException::tooLong($code, self::MAX_LENGTH);
		}

		return self::fromValue($code);
	}
}
