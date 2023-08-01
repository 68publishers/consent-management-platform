<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\DbalType;

use App\Domain\Cookie\ValueObject\CookieId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractUuidIdentityType;

final class CookieIdType extends AbstractUuidIdentityType
{
    protected string $valueObjectClassname = CookieId::class;
}
