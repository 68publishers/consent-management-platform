<?php

declare(strict_types=1);

namespace App\Infrastructure\GlobalSettings\Doctrine\DbalType;

use App\Domain\GlobalSettings\ValueObject\AzureAuthSettings;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractArrayValueObjectType;

final class AzureAuthSettingsType extends AbstractArrayValueObjectType
{
    protected string $valueObjectClassname = AzureAuthSettings::class;
}
