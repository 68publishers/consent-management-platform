<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\ValueObject;

use App\Domain\CookieProvider\Exception\InvalidLinkException;
use Nette\Utils\Validators;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractStringValueObject;

final class Link extends AbstractStringValueObject
{
    public static function withValidation(string $value): self
    {
        if (!empty($value) && !Validators::isUrl($value)) {
            throw InvalidLinkException::invalidUrl($value);
        }

        return self::fromValue($value);
    }
}
