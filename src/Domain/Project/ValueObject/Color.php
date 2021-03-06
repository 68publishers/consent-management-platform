<?php

declare(strict_types=1);

namespace App\Domain\Project\ValueObject;

use App\Domain\Project\Exception\InvalidColorException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractStringValueObject;

final class Color extends AbstractStringValueObject
{
	/**
	 * @param string $color
	 *
	 * @return static
	 */
	public static function fromValidColor(string $color): self
	{
		if (!preg_match('/^#([a-f0-9]{3}){1,2}$/i', $color)) {
			throw InvalidColorException::invalidValue($color);
		}

		return self::fromValue($color);
	}
}
