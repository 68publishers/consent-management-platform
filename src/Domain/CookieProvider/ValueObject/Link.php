<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\ValueObject;

use Nette\Utils\Validators;
use App\Domain\CookieProvider\Exception\InvalidLinkException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractStringValueObject;

final class Link extends AbstractStringValueObject
{
	/**
	 * @param string $value
	 *
	 * @return static
	 */
	public static function withValidation(string $value): self
	{
		if (!Validators::isUrl($value)) {
			throw InvalidLinkException::invalidUrl($value);
		}

		return self::fromValue($value);
	}
}
