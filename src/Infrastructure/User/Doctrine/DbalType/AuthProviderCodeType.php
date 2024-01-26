<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Doctrine\DbalType;

use App\Domain\User\ValueObject\AuthProviderCode;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class AuthProviderCodeType extends AbstractStringValueObjectType
{
    protected string $valueObjectClassname = AuthProviderCode::class;
}
