<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieSuggestion\Doctrine\DbalType;

use App\Domain\CookieSuggestion\ValueObject\Name;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class NameType extends AbstractStringValueObjectType
{
	protected string $valueObjectClassname = Name::class;
}
