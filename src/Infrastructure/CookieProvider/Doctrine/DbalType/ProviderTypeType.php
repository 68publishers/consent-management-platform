<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\DbalType;

use App\Domain\CookieProvider\ValueObject\ProviderType;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class ProviderTypeType extends AbstractStringValueObjectType
{
    protected string $valueObjectClassname = ProviderType::class;
}
