<?php

declare(strict_types=1);

namespace App\Domain\Category\Exception;

use App\Domain\Category\ValueObject\CategoryId;
use DomainException;

final class CategoryNotFoundException extends DomainException
{
    /**
     * @return static
     */
    public static function withId(CategoryId $id): self
    {
        return new self(sprintf(
            'Category with ID %s not found.',
            $id,
        ));
    }
}
