<?php

declare(strict_types=1);

namespace App\Infrastructure\Import\Doctrine\DbalType;

use App\Domain\Import\ValueObject\Output;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractTextValueObjectType;

final class OutputType extends AbstractTextValueObjectType
{
	protected string $valueObjectClassname = Output::class;
}
