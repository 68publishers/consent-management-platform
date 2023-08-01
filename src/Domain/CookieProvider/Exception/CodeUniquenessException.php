<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Exception;

use DomainException;

final class CodeUniquenessException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * @return static
     */
    public static function create(string $code): self
    {
        return new self(sprintf(
            'Cookie provider with a code "%s" already exists.',
            $code,
        ));
    }
}
