<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Exception;

use App\Domain\GlobalSettings\ValueObject\Environment;
use DomainException;

final class UnableToCreateEnvironmentException extends DomainException
{
    public static function nativeMustBeArray(): self
    {
        return new self(sprintf(
            'Unable to create %s from a native value. The native value must be an array.',
            Environment::class,
        ));
    }

    public static function missingNativeKey(string $key): self
    {
        return new self(sprintf(
            'Unable to create %s from a native. The key "%s" is missing.',
            Environment::class,
            $key,
        ));
    }
}
