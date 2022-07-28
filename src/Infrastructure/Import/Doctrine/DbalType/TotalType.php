<?php

declare(strict_types=1);

namespace App\Infrastructure\Import\Doctrine\DbalType;

use App\Domain\Import\ValueObject\Total;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractIntegerValueObjectType;

final class TotalType extends AbstractIntegerValueObjectType
{
	protected string $valueObjectClassname = Total::class;
}
