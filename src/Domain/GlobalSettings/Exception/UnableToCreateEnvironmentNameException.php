<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Exception;

use App\Domain\GlobalSettings\ValueObject\EnvironmentName;
use DomainException;

final class UnableToCreateEnvironmentNameException extends DomainException
{
    public static function nativeMustBeString(): self
    {
        return new self(sprintf(
            'Unable to create %s from a native value. The native value must be a string.',
            EnvironmentName::class,
        ));
    }
}
