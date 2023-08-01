<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Writer;

use App\Application\DataProcessor\Exception\WriterException;
use App\Application\DataProcessor\Write\Destination\DestinationInterface;
use App\Application\DataProcessor\Write\Destination\FileDestination;
use App\Application\DataProcessor\Write\Destination\StringDestination;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;

final class CsvWriterFactory implements WriterFactoryInterface
{
    public function accepts(string $format, DestinationInterface $destination): bool
    {
        return 'csv' === $format && ($destination instanceof FileDestination || $destination instanceof StringDestination);
    }

    public function create(ResourceInterface $resource, DestinationInterface $destination): WriterInterface
    {
        if ($destination instanceof StringDestination) {
            return CsvWriter::fromString($resource, $destination);
        }

        if ($destination instanceof FileDestination) {
            return CsvWriter::fromFile($resource, $destination);
        }

        throw WriterException::unacceptableDestination('csv', $destination);
    }
}
