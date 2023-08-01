<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Exception;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use DomainException;

final class CookieProviderNotFoundException extends DomainException
{
    /**
     * @return static
     */
    public static function withId(CookieProviderId $id): self
    {
        return new self(sprintf(
            'Cookie provider with ID %s not found.',
            $id,
        ));
    }
}
