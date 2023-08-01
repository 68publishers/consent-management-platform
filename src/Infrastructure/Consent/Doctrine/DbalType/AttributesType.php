<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\DbalType;

use App\Domain\Consent\ValueObject\Attributes;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractArrayValueObjectType;

final class AttributesType extends AbstractArrayValueObjectType
{
    protected string $valueObjectClassname = Attributes::class;
}
