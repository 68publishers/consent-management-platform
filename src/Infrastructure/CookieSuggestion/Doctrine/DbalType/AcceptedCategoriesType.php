<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieSuggestion\Doctrine\DbalType;

use App\Domain\CookieSuggestion\ValueObject\AcceptedCategories;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractValueObjectSetType;

final class AcceptedCategoriesType extends AbstractValueObjectSetType
{
    protected string $valueObjectClassname = AcceptedCategories::class;
}
