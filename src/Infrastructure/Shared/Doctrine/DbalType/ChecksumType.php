<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Doctrine\DbalType;

use App\Domain\Shared\ValueObject\Checksum;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class ChecksumType extends AbstractStringValueObjectType
{
    protected string $valueObjectClassname = Checksum::class;
}
