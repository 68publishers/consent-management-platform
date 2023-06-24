<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieSuggestion\Doctrine\DbalType;

use App\Domain\CookieSuggestion\ValueObject\Domain;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class DomainType extends AbstractStringValueObjectType
{
	protected string $valueObjectClassname = Domain::class;
}
