<?php

declare(strict_types=1);

namespace App\Domain\Category;

use App\Domain\Category\Exception\CategoryNotFoundException;
use App\Domain\Category\ValueObject\CategoryId;

interface CategoryRepositoryInterface
{
    public function save(Category $category): void;

    /**
     * @throws CategoryNotFoundException
     */
    public function get(CategoryId $id): Category;
}
