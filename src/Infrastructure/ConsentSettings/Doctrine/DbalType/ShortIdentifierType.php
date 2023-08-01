<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine\DbalType;

use App\Domain\ConsentSettings\ValueObject\ShortIdentifier;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractIntegerValueObjectType;

final class ShortIdentifierType extends AbstractIntegerValueObjectType
{
    protected string $valueObjectClassname = ShortIdentifier::class;
}
