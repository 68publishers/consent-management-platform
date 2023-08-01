<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use App\Application\DataProcessor\Exception\ReaderException;
use App\Application\DataProcessor\Read\Resource\FileResource;
use App\Application\DataProcessor\Read\Resource\ResourceInterface;
use App\Application\DataProcessor\Read\Resource\StringResource;

final class CsvReaderFactory implements ReaderFactoryInterface
{
    public function accepts(string $format, ResourceInterface $resource): bool
    {
        return 'csv' === $format && ($resource instanceof FileResource || $resource instanceof StringResource);
    }

    public function create(ResourceInterface $resource): ReaderInterface
    {
        if ($resource instanceof StringResource) {
            return CsvReader::fromString($resource);
        }

        if ($resource instanceof FileResource) {
            return CsvReader::fromFile($resource);
        }

        throw ReaderException::unacceptableResource('csv', $resource);
    }
}
