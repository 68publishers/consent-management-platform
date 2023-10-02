<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\DbalType;

use App\Domain\Consent\ValueObject\Environment;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class EnvironmentType extends AbstractStringValueObjectType
{
    protected string $valueObjectClassname = Environment::class;
}
