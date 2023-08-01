<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

final class Descriptor
{
    private function __construct() {}

    public static function string(TypeDescriptorPropertyInterface ...$properties): StringDescriptor
    {
        return StringDescriptor::create(...$properties);
    }

    public static function integer(TypeDescriptorPropertyInterface ...$properties): IntegerDescriptor
    {
        return IntegerDescriptor::create(...$properties);
    }

    public static function float(TypeDescriptorPropertyInterface ...$properties): FloatDescriptor
    {
        return FloatDescriptor::create(...$properties);
    }

    public static function boolean(TypeDescriptorPropertyInterface ...$properties): BooleanDescriptor
    {
        return BooleanDescriptor::create(...$properties);
    }

    public static function listOf(DescriptorInterface $valueDescriptor): ListDescriptor
    {
        return ListDescriptor::create($valueDescriptor);
    }

    public static function arrayOf(DescriptorInterface $valueDescriptor, ?DescriptorInterface $keyDescriptor = null): ArrayDescriptor
    {
        return ArrayDescriptor::create($valueDescriptor, $keyDescriptor);
    }

    /**
     * @param array<DescriptorInterface> $descriptors
     */
    public static function structure(array $descriptors = [], StructureDescriptorPropertyInterface ...$properties): StructureDescriptor
    {
        return StructureDescriptor::create($descriptors, ...$properties);
    }
}
