<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use App\Application\DataProcessor\Description\Path\Path;
use App\Application\DataProcessor\Description\Path\PathInfo;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Schema;

final class StructureDescriptor implements DescriptorInterface
{
    /** @var array<DescriptorInterface> */
    private array $descriptors = [];

    /** @var array<StructureDescriptorPropertyInterface> */
    private array $properties = [];

    private function __construct() {}

    /**
     * @param array<DescriptorInterface> $descriptors
     */
    public static function create(array $descriptors, StructureDescriptorPropertyInterface ...$properties): self
    {
        (static fn (DescriptorInterface ...$descriptors) => null)(...array_values($descriptors));

        $structure = new self();
        $structure->descriptors = $descriptors;
        $structure->properties = $properties;

        return $structure;
    }

    public function withDescriptor(string $name, DescriptorInterface $descriptor): self
    {
        $structure = clone $this;
        $structure->descriptors[$name] = $descriptor;

        return $structure;
    }

    public function withProps(StructureDescriptorPropertyInterface ...$properties): self
    {
        $structure = clone $this;
        $structure->properties = array_merge($this->properties, $properties);

        return $structure;
    }

    public function schema(ContextInterface $context): Schema
    {
        $structure = new Structure(
            array_map(
                static fn (DescriptorInterface $descriptor): Schema => $descriptor->schema($context),
                $this->descriptors,
            ),
        );

        $structure->castTo('array');

        foreach ($this->properties as $property) {
            $structure = $property->applyToStructure($structure, $context);
        }

        return $structure;
    }

    public function pathInfo(Path $path): PathInfo
    {
        $part = $path->shift();
        $pathInfo = new PathInfo();

        if (null === $part) {
            $pathInfo->descriptor = $this;
            $pathInfo->found = true;
            $pathInfo->isFinal = false;

            return $pathInfo;
        }

        if (!isset($this->descriptors[$part])) {
            $pathInfo->descriptor = null;
            $pathInfo->found = false;
            $pathInfo->isFinal = false;

            return $pathInfo;
        }

        return $this->descriptors[$part]->pathInfo($path);
    }
}
