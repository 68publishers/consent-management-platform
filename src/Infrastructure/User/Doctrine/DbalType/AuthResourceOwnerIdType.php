<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Doctrine\DbalType;

use App\Domain\User\ValueObject\AuthResourceOwnerId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class AuthResourceOwnerIdType extends AbstractStringValueObjectType
{
    protected string $valueObjectClassname = AuthResourceOwnerId::class;
}
