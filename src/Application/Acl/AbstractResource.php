<?php

declare(strict_types=1);

namespace App\Application\Acl;

use ReflectionClass;

abstract class AbstractResource implements ResourceInterface
{
	private static array $privileges = [];

	/**
	 * {@inheritDoc}
	 *
	 * @throws \ReflectionException
	 */
	public static function privileges(): array
	{
		$classname = static::class;

		if (!isset(self::$privileges[$classname])) {
			$reflection = new ReflectionClass($classname);
			self::$privileges[$classname] = array_values($reflection->getConstants());
		}

		return self::$privileges[$classname];
	}
}
