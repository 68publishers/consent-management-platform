<?php

declare(strict_types=1);

namespace App\Infrastructure\Import\Doctrine\DbalType;

use App\Domain\Import\ValueObject\Author;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class AuthorType extends AbstractStringValueObjectType
{
	protected string $valueObjectClassname = Author::class;
}
