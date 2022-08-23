<?php

declare(strict_types=1);

namespace App\Application\DataProcessor;

use LogicException;
use App\Application\DataProcessor\Description\MapAs;
use App\Application\DataProcessor\Description\Descriptor;
use App\Application\DataProcessor\Description\DescriptorInterface;
use App\Application\DataProcessor\Description\StructureDescriptor;

abstract class AbstractDescribedObject implements RowDataInterface
{
	/**
	 * @return \App\Application\DataProcessor\Description\DescriptorInterface
	 */
	public static function describe(): DescriptorInterface
	{
		$structure = Descriptor::structure([], new MapAs(static::class));

		return static::doDescribe($structure);
	}

	/**
	 * {@inheritDoc}
	 */
	public function has($column): bool
	{
		return isset($this->{$column});
	}

	/**
	 * {@inheritDoc}
	 */
	public function get($column, $default = NULL)
	{
		return $this->{$column} ?? $default;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return array_map(static fn ($var) => $var instanceof RowDataInterface ? $var->toArray() : $var, (array) $this);
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return void
	 * @noinspection MagicMethodsValidityInspection
	 */
	public function __set(string $name, $value): void
	{
		if (property_exists($this, $name)) {
			$this->{$name} = $value;
		}
	}

	/**
	 * @param \App\Application\DataProcessor\Description\StructureDescriptor $descriptor
	 *
	 * @return \App\Application\DataProcessor\Description\StructureDescriptor
	 */
	protected static function doDescribe(StructureDescriptor $descriptor): StructureDescriptor
	{
		throw new LogicException(sprintf(
			'The method %s::doDescribe() must be redeclared.',
			static::class
		));
	}
}
