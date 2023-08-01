<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Exception;

use DomainException;

final class NameUniquenessException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * @return static
     */
    public static function create(string $name, string $cookieProviderId): self
    {
        return new self(sprintf(
            'Cookie with a code "%s" already exists for a cookie provider %s.',
            $name,
            $cookieProviderId,
        ));
    }
}
