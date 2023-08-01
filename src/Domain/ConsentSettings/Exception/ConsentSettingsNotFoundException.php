<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\Exception;

use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use DomainException;

final class ConsentSettingsNotFoundException extends DomainException
{
    /**
     * @return static
     */
    public static function withId(ConsentSettingsId $id): self
    {
        return new self(sprintf(
            'Consent settings with ID %s not found.',
            $id,
        ));
    }
}
