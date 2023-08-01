<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use App\Application\DataProcessor\Description\Path\Path;
use App\Application\DataProcessor\Description\Path\PathInfo;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class ArrayDescriptor implements DescriptorInterface
{
    private DescriptorInterface $valueDescriptor;

    private ?DescriptorInterface $keyDescriptor;

    private function __construct() {}

    /**
     * @return static
     */
    public static function create(DescriptorInterface $valueDescriptor, ?DescriptorInterface $keyDescriptor = null): self
    {
        $descriptor = new self();
        $descriptor->valueDescriptor = $valueDescriptor;
        $descriptor->keyDescriptor = $keyDescriptor;

        return $descriptor;
    }

    public function schema(ContextInterface $context): Schema
    {
        return Expect::arrayOf(
            $this->valueDescriptor->schema($context),
            $this->keyDescriptor?->schema($context),
        );
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

        return $this->valueDescriptor->pathInfo($path);
    }
}
