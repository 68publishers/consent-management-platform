<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieSuggestion\Doctrine\DbalType;

use App\Domain\CookieSuggestion\ValueObject\ScenarioName;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class ScenarioNameType extends AbstractStringValueObjectType
{
    protected string $valueObjectClassname = ScenarioName::class;
}
