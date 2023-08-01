<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\DbalType;

use App\Domain\Project\ValueObject\Name;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class NameType extends AbstractStringValueObjectType
{
    protected string $valueObjectClassname = Name::class;
}
