<?php

declare(strict_types=1);

namespace App\Domain\Category\ValueObject;

use App\Domain\Category\Exception\InvalidCodeException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractStringValueObject;

final class Code extends AbstractStringValueObject
{
    public const int MAX_LENGTH = 70;

    public static function fromValidCode(string $code): self
    {
        if (self::MAX_LENGTH < mb_strlen($code)) {
            throw InvalidCodeException::tooLong($code, self::MAX_LENGTH);
        }

        return self::fromValue($code);
    }
}
