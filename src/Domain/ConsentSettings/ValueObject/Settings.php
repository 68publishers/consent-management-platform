<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\ValueObject;

use DateTimeZone;
use DateTimeImmutable;
use DateTimeInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractArrayValueObject;

final class Settings extends AbstractArrayValueObject
{
	/**
	 * @param array                   $settings
	 * @param \DateTimeImmutable|NULL $createdAt
	 *
	 * @return static
	 * @throws \Exception
	 */
	public static function create(array $settings, ?DateTimeImmutable $createdAt = NULL): self
	{
		$createdAt = $createdAt ?? new DateTimeImmutable('now', new DateTimeZone('UTC'));
		$settings = [
			'settings' => $settings,
			'metadata' => [
				'created_at' => $createdAt->format(DateTimeInterface::ATOM),
			],
		];

		return self::fromArray($settings);
	}

	/**
	 * @param array $left
	 * @param array $right
	 *
	 * @return bool
	 */
	protected function doCompareValues(array $left, array $right): bool
	{
		return parent::doCompareValues($left['settings'], $right['settings']);
	}
}
