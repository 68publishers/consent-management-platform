<?php

declare(strict_types=1);

namespace App\Infrastructure\GlobalSettings\Doctrine\DbalType;

use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractUuidIdentityType;

final class GlobalSettingsIdType extends AbstractUuidIdentityType
{
	protected string $valueObjectClassname = GlobalSettingsId::class;
}
