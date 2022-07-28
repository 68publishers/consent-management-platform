<?php

declare(strict_types=1);

namespace App\Application\DataReader;

use LogicException;
use App\Application\DataReader\Description\MapAs;
use App\Application\DataReader\Description\Descriptor;
use App\Application\DataReader\Description\DescriptorInterface;
use App\Application\DataReader\Description\StructureDescriptor;

abstract class AbstractDescribedObject implements RowDataInterface
{
	/**
	 * @return \App\Application\DataReader\Description\DescriptorInterface
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
	 * @param \App\Application\DataReader\Description\StructureDescriptor $descriptor
	 *
	 * @return \App\Application\DataReader\Description\StructureDescriptor
	 */
	protected static function doDescribe(StructureDescriptor $descriptor): StructureDescriptor
	{
		throw new LogicException(sprintf(
			'The method %s::doDescribe() must be redeclared.',
			static::class
		));
	}
}
