<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

final class Descriptor
{
	private function __construct()
	{
	}

	/**
	 * @param \App\Application\DataProcessor\Description\TypeDescriptorPropertyInterface ...$properties
	 *
	 * @return \App\Application\DataProcessor\Description\StringDescriptor
	 */
	public static function string(TypeDescriptorPropertyInterface ...$properties): StringDescriptor
	{
		return StringDescriptor::create(...$properties);
	}

	/**
	 * @param \App\Application\DataProcessor\Description\TypeDescriptorPropertyInterface ...$properties
	 *
	 * @return \App\Application\DataProcessor\Description\IntegerDescriptor
	 */
	public static function integer(TypeDescriptorPropertyInterface ...$properties): IntegerDescriptor
	{
		return IntegerDescriptor::create(...$properties);
	}

	/**
	 * @param \App\Application\DataProcessor\Description\TypeDescriptorPropertyInterface ...$properties
	 *
	 * @return \App\Application\DataProcessor\Description\FloatDescriptor
	 */
	public static function float(TypeDescriptorPropertyInterface ...$properties): FloatDescriptor
	{
		return FloatDescriptor::create(...$properties);
	}

	/**
	 * @param \App\Application\DataProcessor\Description\TypeDescriptorPropertyInterface ...$properties
	 *
	 * @return \App\Application\DataProcessor\Description\BooleanDescriptor
	 */
	public static function boolean(TypeDescriptorPropertyInterface ...$properties): BooleanDescriptor
	{
		return BooleanDescriptor::create(...$properties);
	}

	/**
	 * @param \App\Application\DataProcessor\Description\DescriptorInterface $valueDescriptor
	 *
	 * @return \App\Application\DataProcessor\Description\ListDescriptor
	 */
	public static function listOf(DescriptorInterface $valueDescriptor): ListDescriptor
	{
		return ListDescriptor::create($valueDescriptor);
	}

	/**
	 * @param \App\Application\DataProcessor\Description\DescriptorInterface      $valueDescriptor
	 * @param \App\Application\DataProcessor\Description\DescriptorInterface|NULL $keyDescriptor
	 *
	 * @return \App\Application\DataProcessor\Description\ArrayDescriptor
	 */
	public static function arrayOf(DescriptorInterface $valueDescriptor, ?DescriptorInterface $keyDescriptor = NULL): ArrayDescriptor
	{
		return ArrayDescriptor::create($valueDescriptor, $keyDescriptor);
	}

	/**
	 * @param \App\Application\DataProcessor\Description\DescriptorInterface[]                $descriptors
	 * @param \App\Application\DataProcessor\Description\StructureDescriptorPropertyInterface ...$properties
	 *
	 * @return \App\Application\DataProcessor\Description\StructureDescriptor
	 */
	public static function structure(array $descriptors = [], StructureDescriptorPropertyInterface ...$properties): StructureDescriptor
	{
		return StructureDescriptor::create($descriptors, ...$properties);
	}
}
