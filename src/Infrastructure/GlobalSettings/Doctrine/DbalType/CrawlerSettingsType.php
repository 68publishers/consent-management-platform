<?php

declare(strict_types=1);

namespace App\Infrastructure\GlobalSettings\Doctrine\DbalType;

use App\Domain\GlobalSettings\ValueObject\CrawlerSettings;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractArrayValueObjectType;

final class CrawlerSettingsType extends AbstractArrayValueObjectType
{
    protected string $valueObjectClassname = CrawlerSettings::class;
}
