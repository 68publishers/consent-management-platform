<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\DbalType;

use App\Domain\Category\ValueObject\Code;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class CodeType extends AbstractStringValueObjectType
{
	protected string $valueObjectClassname = Code::class;
}
