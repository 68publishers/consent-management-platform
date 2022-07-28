<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObject;

use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractEnumValueObject;

final class Status extends AbstractEnumValueObject
{
	public const RUNNING = 'running';
	public const FAILED = 'failed';
	public const COMPLETED = 'completed';

	/**
	 * {@inheritDoc}
	 */
	public static function values(): array
	{
		return [
			self::RUNNING,
			self::FAILED,
			self::COMPLETED,
		];
	}

	/**
	 * @return static
	 */
	public static function running(): self
	{
		return self::fromValue(self::RUNNING);
	}

	/**
	 * @return static
	 */
	public static function failed(): self
	{
		return self::fromValue(self::FAILED);
	}

	/**
	 * @return static
	 */
	public static function completed(): self
	{
		return self::fromValue(self::COMPLETED);
	}
}
