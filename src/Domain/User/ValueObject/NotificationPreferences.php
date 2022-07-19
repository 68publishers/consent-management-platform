<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractValueObjectSet;

final class NotificationPreferences extends AbstractValueObjectSet
{
	public const ITEM_CLASSNAME = NotificationType::class;

	/**
	 * @param string $value
	 *
	 * @return \App\Domain\User\ValueObject\NotificationType
	 */
	protected static function reconstituteItem($value): NotificationType
	{
		return NotificationType::fromValue($value);
	}

	/**
	 * @param \App\Domain\User\ValueObject\NotificationType $item
	 *
	 * @return string
	 */
	protected static function exportItem($item): string
	{
		assert($item instanceof NotificationType);

		return $item->value();
	}
}
