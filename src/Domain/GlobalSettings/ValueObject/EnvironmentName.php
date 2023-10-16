<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\ValueObject;

use App\Domain\GlobalSettings\Exception\UnableToCreateEnvironmentNameException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractStringValueObject;

final class EnvironmentName extends AbstractStringValueObject
{
    /**
     * @throws UnableToCreateEnvironmentNameException
     */
    public static function fromNative(mixed $native): self
    {
        if (!is_string($native)) {
            throw UnableToCreateEnvironmentNameException::nativeMustBeString();
        }

        return self::fromValue($native);
    }

    public static function fromSafeNative(mixed $native): self
    {
        assert(is_string($native));

        return self::fromValue($native);
    }
}
