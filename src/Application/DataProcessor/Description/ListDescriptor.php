<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use App\Application\DataProcessor\Description\Path\Path;
use App\Application\DataProcessor\Description\Path\PathInfo;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class ListDescriptor implements DescriptorInterface
{
    private DescriptorInterface $valueDescriptor;

    private function __construct() {}

    public static function create(DescriptorInterface $valueDescriptor): self
    {
        $descriptor = new self();
        $descriptor->valueDescriptor = $valueDescriptor;

        return $descriptor;
    }

    public function schema(ContextInterface $context): Schema
    {
        $list = Expect::listOf(
            $this->valueDescriptor->schema($context),
        );

        if (true === ($context[ContextInterface::WEAK_TYPES] ?? false)) {
            $list->before(function ($value) {
                if (!is_scalar($value)) {
                    return $value;
                }

                $value = trim((string) $value);

                if ('' === $value) {
                    return [];
                }

                $value = explode(',', $value);

                return array_map('trim', $value);
            });
        }

        return $list;
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

        if (!is_numeric($part)) {
            $pathInfo->descriptor = null;
            $pathInfo->found = false;
            $pathInfo->isFinal = false;

            return $pathInfo;
        }

        return $this->valueDescriptor->pathInfo($path);
    }
}
