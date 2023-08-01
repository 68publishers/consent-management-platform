<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Exception;

use App\Application\DataProcessor\Write\Destination\DestinationInterface;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;
use RuntimeException;
use Throwable;

final class WriterException extends RuntimeException
{
    /**
     * @return static
     */
    public static function writerLocked(ResourceInterface $resource, DestinationInterface $destination): self
    {
        return new self(sprintf(
            'Can not write the resource %s into the destination %s. Writing process is still in progress.',
            $resource,
            $destination,
        ));
    }

    /**
     * @return static
     */
    public static function wrap(ResourceInterface $resource, DestinationInterface $destination, Throwable $e): self
    {
        return new self(sprintf(
            'An exception occurred during writing the resource %s into the destination %s: %s',
            $resource,
            $destination,
            $e->getMessage(),
        ), $e->getCode(), $e);
    }

    /**
     * @return static
     */
    public static function unacceptableDestination(string $format, DestinationInterface $destination): self
    {
        return new self(sprintf(
            'Unacceptable destination: %s writer doesn\'t accept destination %s',
            $format,
            $destination,
        ));
    }

    /**
     * @return static
     */
    public static function unresolvableDestination(string $format, DestinationInterface $destination): self
    {
        return new self(sprintf(
            'Can\'t resolve writer for format %s and destination %s',
            $format,
            $destination,
        ));
    }
}
