<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Context;

use InvalidArgumentException;

final class Context implements ContextInterface
{
	private array $context = [];

	private function __construct()
	{
	}

	/**
	 * @param array $array
	 *
	 * @return \App\Application\DataProcessor\Context\ContextInterface
	 */
	public static function default(array $array = []): ContextInterface
	{
		return self::fromArray(array_merge([
			self::WEAK_TYPES => FALSE,
		], $array));
	}

	/**
	 * {@inheritDoc}
	 */
	public static function fromArray(array $array): ContextInterface
	{
		$context = new self();
		$context->context = $array;

		return $context;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetExists($offset): bool
	{
		return $this->exists($offset, FALSE);
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetGet($offset)
	{
		$this->exists($offset);

		return $this->context[$offset];
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetSet($offset, $value): void
	{
		$this->context[$offset] = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetUnset($offset): void
	{
		$this->exists($offset);

		unset($this->context[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @param bool  $throw
	 *
	 * @return bool
	 */
	private function exists($offset, bool $throw = TRUE): bool
	{
		$exists = array_key_exists($offset, $this->context);

		if (!$exists && $throw) {
			throw new InvalidArgumentException(sprintf(
				'Missing context options %s.',
				$offset
			));
		}

		return $exists;
	}
}
