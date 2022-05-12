<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\ValueObject;

use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractValueObjectSet;

final class SettingsGroup extends AbstractValueObjectSet
{
	public const ITEM_CLASSNAME = Settings::class;

	/**
	 * @param mixed $value
	 *
	 * @return \App\Domain\ConsentSettings\ValueObject\Settings
	 */
	protected static function reconstituteItem($value): Settings
	{
		return Settings::fromArray($value);
	}

	/**
	 * @param \App\Domain\ConsentSettings\ValueObject\Settings $item
	 *
	 * @return array
	 */
	protected static function exportItem($item): array
	{
		assert($item instanceof Settings);

		return $item->values();
	}
}
