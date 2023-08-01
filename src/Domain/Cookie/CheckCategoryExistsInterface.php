<?php

declare(strict_types=1);

namespace App\Domain\Cookie;

use App\Domain\Category\Exception\CategoryNotFoundException;
use App\Domain\Category\ValueObject\CategoryId;

interface CheckCategoryExistsInterface
{
    /**
     * @throws CategoryNotFoundException
     */
    public function __invoke(CategoryId $categoryId): void;
}
