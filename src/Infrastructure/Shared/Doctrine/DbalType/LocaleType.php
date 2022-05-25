<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Doctrine\DbalType;

use App\Domain\Shared\ValueObject\Locale;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class LocaleType extends AbstractStringValueObjectType
{
	protected string $valueObjectClassname = Locale::class;
}
