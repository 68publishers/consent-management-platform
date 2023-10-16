<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\ValueObject;

use App\Domain\GlobalSettings\Exception\UnableToCreateEnvironmentCodeException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractStringValueObject;

final class EnvironmentCode extends AbstractStringValueObject
{
    /**
     * @throws UnableToCreateEnvironmentCodeException
     */
    public static function fromNative(mixed $native): self
    {
        if (!is_string($native)) {
            throw UnableToCreateEnvironmentCodeException::nativeMustBeString();
        }

        if (!preg_match('/^[a-z0-9_\-\.]+$/', $native)) {
            throw UnableToCreateEnvironmentCodeException::nativeContainsInvalidCharacters($native);
        }

        return self::fromValue($native);
    }

    public static function fromSafeNative(mixed $native): self
    {
        assert(is_string($native) && preg_match('/^[a-z0-9_\-\.]+$/', $native));

        return self::fromValue($native);
    }
}
