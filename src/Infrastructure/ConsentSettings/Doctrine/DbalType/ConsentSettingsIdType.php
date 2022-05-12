<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine\DbalType;

use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractUuidIdentityType;

final class ConsentSettingsIdType extends AbstractUuidIdentityType
{
	protected string $valueObjectClassname = ConsentSettingsId::class;
}
