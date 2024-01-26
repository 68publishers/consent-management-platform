<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Doctrine\DbalType;

use App\Domain\User\ValueObject\AuthToken;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractTextValueObjectType;

final class AuthTokenType extends AbstractTextValueObjectType
{
    protected string $valueObjectClassname = AuthToken::class;
}
