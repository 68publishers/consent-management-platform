<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\DbalType;

use App\Domain\Consent\ValueObject\Consents;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractArrayValueObjectType;

final class ConsentsType extends AbstractArrayValueObjectType
{
    protected string $valueObjectClassname = Consents::class;
}
