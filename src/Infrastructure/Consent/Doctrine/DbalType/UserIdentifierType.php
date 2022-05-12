<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\DbalType;

use App\Domain\Consent\ValueObject\UserIdentifier;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class UserIdentifierType extends AbstractStringValueObjectType
{
	protected string $valueObjectClassname = UserIdentifier::class;
}
