<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\ValueObject;

use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractValueObjectSet;

final class Environments extends AbstractValueObjectSet
{
    public const ITEM_CLASSNAME = Environment::class;

    public function getByCode(string $code): ?Environment
    {
        foreach ($this->items as $item) {
            assert($item instanceof Environment);

            if ($item->code === $code) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param array $value
     */
    protected static function reconstituteItem($value): Environment
    {
        return Environment::fromSafeNative($value);
    }

    /**
     * @param Environment $item
     *
     * @return array{
     *     code: string,
     *     name: string,
     *     color: string,
     * }
     */
    protected static function exportItem($item): array
    {
        assert($item instanceof Environment);

        return $item->toNative();
    }
}
