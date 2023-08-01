<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\DbalType;

use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractUuidIdentityType;

final class ProjectIdType extends AbstractUuidIdentityType
{
    protected string $valueObjectClassname = ProjectId::class;
}
