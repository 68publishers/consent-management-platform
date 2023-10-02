<?php

declare(strict_types=1);

namespace App\Infrastructure\GlobalSettings\Doctrine\DbalType;

use App\Domain\GlobalSettings\ValueObject\Environments;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractValueObjectSetType;

final class EnvironmentsType extends AbstractValueObjectSetType
{
    protected string $valueObjectClassname = Environments::class;
}
