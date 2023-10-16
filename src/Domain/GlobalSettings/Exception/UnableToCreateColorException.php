<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Exception;

use App\Domain\GlobalSettings\ValueObject\Color;
use DomainException;

final class UnableToCreateColorException extends DomainException
{
    public static function nativeMustBeString(): self
    {
        return new self(sprintf(
            'Unable to create %s from a native value. The native value must be a string.',
            Color::class,
        ));
    }

    public static function nativeMustBeInHexFormat(string $native): self
    {
        return new self(sprintf(
            'Unable to create %s from a native value "%s". The native value must be a color in hex format.',
            Color::class,
            $native,
        ));
    }
}
