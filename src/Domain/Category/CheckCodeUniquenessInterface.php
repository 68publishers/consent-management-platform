<?php

declare(strict_types=1);

namespace App\Domain\Category;

use App\Domain\Category\Exception\CodeUniquenessException;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Category\ValueObject\Code;

interface CheckCodeUniquenessInterface
{
    /**
     * @throws CodeUniquenessException
     */
    public function __invoke(CategoryId $categoryId, Code $code): void;
}
