<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Exception;

use App\Domain\Cookie\ValueObject\ProcessingTime;
use DomainException;

final class InvalidProcessingTimeException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * @return static
     */
    public static function invalidValue(string $value): self
    {
        return new self(sprintf(
            'Processing time must be "%s" or "%s" or valid estimate mask. String "%s" given.',
            ProcessingTime::PERSISTENT,
            ProcessingTime::SESSION,
            $value,
        ));
    }
}
