<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\DbalType;

use App\Domain\Cookie\ValueObject\ProcessingTime;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class ProcessingTimeType extends AbstractStringValueObjectType
{
    protected string $valueObjectClassname = ProcessingTime::class;
}
