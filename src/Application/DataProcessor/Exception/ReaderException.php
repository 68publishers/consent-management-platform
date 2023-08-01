<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Exception;

use App\Application\DataProcessor\Read\Resource\ResourceInterface;
use RuntimeException;

final class ReaderException extends RuntimeException implements DataReaderExceptionInterface
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function invalidResource(string $message): self
    {
        return new self(sprintf(
            'Invalid resource: %s',
            $message,
        ));
    }

    public static function unacceptableResource(string $format, ResourceInterface $resource): self
    {
        return new self(sprintf(
            'Unacceptable resource: %s reader doesn\'t accept resource %s',
            $format,
            $resource,
        ));
    }

    public static function unresolvableResource(string $format, ResourceInterface $resource): self
    {
        return new self(sprintf(
            'Can\'t resolve reader for format %s and resource %s',
            $format,
            $resource,
        ));
    }
}
