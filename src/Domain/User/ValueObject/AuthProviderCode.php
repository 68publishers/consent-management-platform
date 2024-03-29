<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractStringValueObject;

final class AuthProviderCode extends AbstractStringValueObject
{
    public function __toString(): string
    {
        return $this->value();
    }
}
