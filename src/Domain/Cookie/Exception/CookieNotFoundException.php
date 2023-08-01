<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Exception;

use App\Domain\Cookie\ValueObject\CookieId;
use DomainException;

final class CookieNotFoundException extends DomainException
{
    public static function withId(CookieId $id): self
    {
        return new self(sprintf(
            'Cookie with ID %s not found.',
            $id,
        ));
    }
}
