<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\DbalType;

use App\Domain\Category\ValueObject\CategoryId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractUuidIdentityType;

final class CategoryIdType extends AbstractUuidIdentityType
{
	protected string $valueObjectClassname = CategoryId::class;
}
