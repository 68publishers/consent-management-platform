<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractValueObjectSet;

final class Locales extends AbstractValueObjectSet
{
	public const ITEM_CLASSNAME = Locale::class;

	/**
	 * @param string $value
	 *
	 * @return \App\Domain\Shared\ValueObject\Locale
	 */
	protected static function reconstituteItem($value): Locale
	{
		return Locale::fromValue($value);
	}

	/**
	 * @param \App\Domain\Shared\ValueObject\Locale $item
	 *
	 * @return string
	 */
	protected static function exportItem($item): string
	{
		assert($item instanceof Locale);

		return $item->value();
	}
}
