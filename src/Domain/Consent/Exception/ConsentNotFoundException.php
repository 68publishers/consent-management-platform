<?php

declare(strict_types=1);

namespace App\Domain\Consent\Exception;

use App\Domain\Consent\ValueObject\ConsentId;
use DomainException;

final class ConsentNotFoundException extends DomainException
{
    public static function withId(ConsentId $id): self
    {
        return new self(sprintf(
            'Consent with ID %s not found.',
            $id,
        ));
    }
}
