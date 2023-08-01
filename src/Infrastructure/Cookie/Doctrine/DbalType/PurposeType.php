<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\DbalType;

use App\Domain\Cookie\ValueObject\Purpose;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractTextValueObjectType;

final class PurposeType extends AbstractTextValueObjectType
{
    protected string $valueObjectClassname = Purpose::class;
}
