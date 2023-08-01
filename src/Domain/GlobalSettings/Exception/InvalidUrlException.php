<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Exception;

use DomainException;

final class InvalidUrlException extends DomainException
{
    public static function create(string $invalidUrl): self
    {
        return new self(sprintf(
            'Value %s is not valid url.',
            $invalidUrl,
        ));
    }
}
