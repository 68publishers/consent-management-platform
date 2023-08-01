<?php

declare(strict_types=1);

namespace App\Application\DataProcessor;

use App\Application\DataProcessor\Description\Descriptor;
use App\Application\DataProcessor\Description\DescriptorInterface;
use App\Application\DataProcessor\Description\MapAs;
use App\Application\DataProcessor\Description\StructureDescriptor;
use LogicException;

abstract class AbstractDescribedObject implements RowDataInterface
{
    public static function describe(): DescriptorInterface
    {
        $structure = Descriptor::structure([], new MapAs(static::class));

        return static::doDescribe($structure);
    }

    public function has(string|int $column): bool
    {
        return isset($this->{$column});
    }

    public function get(string|int $column, mixed $default = null): mixed
    {
        return $this->{$column} ?? $default;
    }

    public function toArray(): array
    {
        return array_map(static fn ($var) => $var instanceof RowDataInterface ? $var->toArray() : $var, (array) $this);
    }

    /**
     * @noinspection MagicMethodsValidityInspection
     */
    public function __set(string $name, mixed $value): void
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        }
    }

    protected static function doDescribe(StructureDescriptor $descriptor): StructureDescriptor
    {
        throw new LogicException(sprintf(
            'The method %s::doDescribe() must be redeclared.',
            static::class,
        ));
    }
}
