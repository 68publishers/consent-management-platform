<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Writer;

use App\Application\DataProcessor\Exception\WriterException;
use App\Application\DataProcessor\Write\Destination\DestinationInterface;
use App\Application\DataProcessor\Write\Destination\FileDestination;
use App\Application\DataProcessor\Write\Destination\StringDestination;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;

final class JsonWriterFactory implements WriterFactoryInterface
{
    public function accepts(string $format, DestinationInterface $destination): bool
    {
        return 'json' === $format && ($destination instanceof FileDestination || $destination instanceof StringDestination);
    }

    public function create(ResourceInterface $resource, DestinationInterface $destination): WriterInterface
    {
        if ($destination instanceof StringDestination) {
            return JsonWriter::fromString($resource, $destination);
        }

        if ($destination instanceof FileDestination) {
            return JsonWriter::fromFile($resource, $destination);
        }

        throw WriterException::unacceptableDestination('json', $destination);
    }
}
