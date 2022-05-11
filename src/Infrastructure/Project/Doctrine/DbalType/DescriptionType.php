<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\DbalType;

use App\Domain\Project\ValueObject\Description;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractTextValueObjectType;

final class DescriptionType extends AbstractTextValueObjectType
{
	protected string $valueObjectClassname = Description::class;
}
