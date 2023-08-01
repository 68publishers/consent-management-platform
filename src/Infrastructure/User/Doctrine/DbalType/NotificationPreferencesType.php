<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Doctrine\DbalType;

use App\Domain\User\ValueObject\NotificationPreferences;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractValueObjectSetType;

final class NotificationPreferencesType extends AbstractValueObjectSetType
{
    protected string $valueObjectClassname = NotificationPreferences::class;
}
