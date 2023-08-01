<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieSuggestion\Doctrine\DbalType;

use App\Domain\CookieSuggestion\ValueObject\FoundOnUrl;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractTextValueObjectType;

final class FoundOnUrlType extends AbstractTextValueObjectType
{
    protected string $valueObjectClassname = FoundOnUrl::class;
}
