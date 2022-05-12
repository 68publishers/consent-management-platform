<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\DbalType;

use App\Domain\Consent\ValueObject\ConsentId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractUuidIdentityType;

final class ConsentIdType extends AbstractUuidIdentityType
{
	protected string $valueObjectClassname = ConsentId::class;
}
