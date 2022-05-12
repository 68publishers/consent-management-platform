<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine\DbalType;

use App\Domain\ConsentSettings\ValueObject\SettingsGroup;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractValueObjectSetType;

final class SettingsGroupType extends AbstractValueObjectSetType
{
	protected string $valueObjectClassname = SettingsGroup::class;
}
