<?php

declare(strict_types=1);

namespace App\Domain\Project\Exception;

use DomainException;

final class InvalidCodeException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function containsNonAllowedCharacters(string $code): self
    {
        return new self(sprintf(
            'Code "%s" contains non allowed characters.',
            $code,
        ));
    }

    public static function tooLong(string $code, int $maxLength): self
    {
        return new self(sprintf(
            'Code "%s" is too long, maximal allowed length is %d characters.',
            $code,
            $maxLength,
        ));
    }
}
