<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\DbalType;

use App\Domain\Project\ValueObject\Domain;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class DomainType extends AbstractStringValueObjectType
{
    protected string $valueObjectClassname = Domain::class;
}
