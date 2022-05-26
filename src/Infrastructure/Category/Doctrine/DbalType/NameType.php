<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\DbalType;

use App\Domain\Category\ValueObject\Name;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractTextValueObjectType;

final class NameType extends AbstractTextValueObjectType
{
	protected string $valueObjectClassname = Name::class;
}
