<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieSuggestion\Doctrine\DbalType;

use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractUuidIdentityType;

final class CookieSuggestionIdType extends AbstractUuidIdentityType
{
	protected string $valueObjectClassname = CookieSuggestionId::class;
}
