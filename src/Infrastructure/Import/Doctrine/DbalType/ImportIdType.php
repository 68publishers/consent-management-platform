<?php

declare(strict_types=1);

namespace App\Infrastructure\Import\Doctrine\DbalType;

use App\Domain\Import\ValueObject\ImportId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractUuidIdentityType;

final class ImportIdType extends AbstractUuidIdentityType
{
    protected string $valueObjectClassname = ImportId::class;
}
