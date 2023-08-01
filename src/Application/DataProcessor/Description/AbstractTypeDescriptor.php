<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use App\Application\DataProcessor\Description\Path\Path;
use App\Application\DataProcessor\Description\Path\PathInfo;
use Nette\Schema\Elements\Type;
use Nette\Schema\Schema;

abstract class AbstractTypeDescriptor implements DescriptorInterface
{
    /** @var TypeDescriptorPropertyInterface[] */
    private array $properties = [];

    private function __construct() {}

    /**
     * @return static
     */
    public static function create(TypeDescriptorPropertyInterface ...$properties): self
    {
        $descriptor = new static();

        if (!empty($properties)) {
            $descriptor = $descriptor->withProps(...$properties);
        }

        return $descriptor;
    }

    public function schema(ContextInterface $context): Schema
    {
        $type = $this->createType($context);

        foreach ($this->properties as $property) {
            $type = $property->applyToType($type, $context);
        }

        return $type;
    }

    public function pathInfo(Path $path): PathInfo
    {
        $part = $path->shift();
        $pathInfo = new PathInfo();

        if (null === $part) {
            $pathInfo->descriptor = $this;
            $pathInfo->found = true;
            $pathInfo->isFinal = true;

            return $pathInfo;
        }

        $pathInfo->descriptor = null;
        $pathInfo->found = false;
        $pathInfo->isFinal = false;

        return $pathInfo;
    }

    /**
     * @return $this
     */
    public function withProps(TypeDescriptorPropertyInterface ...$properties): self
    {
        $descriptor = clone $this;
        $descriptor->properties = array_merge($this->properties, $properties);

        return $descriptor;
    }

    abstract protected function createType(ContextInterface $context): Type;

    protected function tryToConvertWeakNullValue(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $val = trim($value);

        if (('' === $val && !$this->isAnyOf([Required::class]))
            || (('null' === $val || 'NULL' === $val) && $this->isAnyOf([Nullable::class]))
        ) {
            $value = null;
        }

        return $value;
    }

    /**
     * @param string[] $propertyClassnames
     */
    private function isAnyOf(array $propertyClassnames): bool
    {
        return 0 < count(
            array_filter(
                $this->properties,
                static fn (TypeDescriptorPropertyInterface $property): bool => in_array(get_class($property), $propertyClassnames, true),
            ),
        );
    }
}
