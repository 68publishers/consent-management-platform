<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\DbalType;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractUuidIdentityType;

final class CookieProviderIdType extends AbstractUuidIdentityType
{
	protected string $valueObjectClassname = CookieProviderId::class;
}
