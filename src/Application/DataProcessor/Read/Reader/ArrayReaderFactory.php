<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use App\Application\DataProcessor\Exception\ReaderException;
use App\Application\DataProcessor\Read\Resource\ArrayResource;
use App\Application\DataProcessor\Read\Resource\ResourceInterface;

final class ArrayReaderFactory implements ReaderFactoryInterface
{
    public function accepts(string $format, ResourceInterface $resource): bool
    {
        return 'array' === $format && $resource instanceof ArrayResource;
    }

    public function create(ResourceInterface $resource): ReaderInterface
    {
        if ($resource instanceof ArrayResource) {
            return ArrayReader::fromArray($resource);
        }

        throw ReaderException::unacceptableResource('array', $resource);
    }
}
