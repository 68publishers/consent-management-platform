<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\ValueObject;

use App\Domain\GlobalSettings\Exception\UnableToCreateColorException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractStringValueObject;

final class Color extends AbstractStringValueObject
{
    /**
     * @throws UnableToCreateColorException
     */
    public static function fromNative(mixed $color): self
    {
        if (!is_string($color)) {
            throw UnableToCreateColorException::nativeMustBeString();
        }

        if (!preg_match('/^#([a-fA-F\d]{3}){1,2}$/i', $color)) {
            throw UnableToCreateColorException::nativeMustBeInHexFormat($color);
        }

        return self::fromValue($color);
    }

    public static function fromSafeNative(mixed $color): self
    {
        assert(is_string($color) && preg_match('/^#([a-fA-F\d]{3}){1,2}$/i', $color));

        return self::fromValue($color);
    }

    public function isWhite(): bool
    {
        return in_array($this->value, ['#fff', '#ffffff'], true);
    }
}
