<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\DbalType;

use App\Domain\Cookie\ValueObject\Domain;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class DomainType extends AbstractStringValueObjectType
{
	protected string $valueObjectClassname = Domain::class;
}
