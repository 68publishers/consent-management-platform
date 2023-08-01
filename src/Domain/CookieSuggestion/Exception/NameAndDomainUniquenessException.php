<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Exception;

use DomainException;

final class NameAndDomainUniquenessException extends DomainException
{
    public static function create(string $name, string $domain, string $projectId): self
    {
        return new self(sprintf(
            'Cookie suggestion with a name "%s" and domain "%s" already exists for the project %s.',
            $name,
            $domain,
            $projectId,
        ));
    }
}
