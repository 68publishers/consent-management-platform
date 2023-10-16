<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Exception;

use App\Domain\GlobalSettings\ValueObject\EnvironmentCode;
use DomainException;

final class UnableToCreateEnvironmentCodeException extends DomainException
{
    public static function nativeMustBeString(): self
    {
        return new self(sprintf(
            'Unable to create %s from a native value. The native value must be a string.',
            EnvironmentCode::class,
        ));
    }

    public static function nativeContainsInvalidCharacters(string $native): self
    {
        return new self(sprintf(
            'Unable to create %s from a native value "%s". The native value contains invalid characters.',
            EnvironmentCode::class,
            $native,
        ));
    }
}
