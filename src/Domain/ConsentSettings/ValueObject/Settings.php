<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\ValueObject;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractArrayValueObject;

final class Settings extends AbstractArrayValueObject
{
    /**
     * @return static
     * @throws Exception
     */
    public static function create(array $settings, ?DateTimeImmutable $createdAt = null): self
    {
        $createdAt = $createdAt ?? new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $settings = [
            'settings' => $settings,
            'metadata' => [
                'created_at' => $createdAt->format(DateTimeInterface::ATOM),
            ],
        ];

        return self::fromArray($settings);
    }

    protected function doCompareValues(array $left, array $right): bool
    {
        return parent::doCompareValues($left['settings'], $right['settings']);
    }
}
