<?php

declare(strict_types=1);

namespace App\ReadModel\Category;

use App\ReadModel\AbstractDataGridQuery;

/**
 * Returns `array<CategoryView>`
 */
final class CategoriesDataGridQuery extends AbstractDataGridQuery
{
    public static function create(?string $locale): self
    {
        return self::fromParameters([
            'locale' => $locale,
        ]);
    }

    public function locale(): ?string
    {
        return $this->getParam('locale');
    }
}
