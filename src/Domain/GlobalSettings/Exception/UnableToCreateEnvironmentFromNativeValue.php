<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Exception;

use DomainException;

final class UnableToCreateEnvironmentFromNativeValue extends DomainException
{
    public static function nativeMustBeArray(): self
    {
        return new self('Unable to create Environment value object from a native. The native must be an array.');
    }

    public static function missingNativeKey(string $key): self
    {
        return new self(sprintf(
            'Unable to create Environment value object from a native. The key "%s" is missing.',
            $key,
        ));
    }

    public static function invalidNativeValueType(string $key, string $expectedType): self
    {
        return new self(sprintf(
            'Unable to create Environment value object from a native. The value of the key "%s" must be %s.',
            $key,
            $expectedType,
        ));
    }
}
