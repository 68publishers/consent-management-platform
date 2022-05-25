<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Doctrine\DbalType;

use App\Domain\Shared\ValueObject\Locales;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractValueObjectSetType;

final class LocalesType extends AbstractValueObjectSetType
{
	protected string $valueObjectClassname = Locales::class;
}
