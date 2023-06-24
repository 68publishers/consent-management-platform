<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieSuggestion\Doctrine\DbalType;

use App\Domain\CookieSuggestion\ValueObject\CookieOccurrenceId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractUuidIdentityType;

final class CookieOccurrenceIdType extends AbstractUuidIdentityType
{
	protected string $valueObjectClassname = CookieOccurrenceId::class;
}
