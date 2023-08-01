<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryForm\Event;

use App\Domain\Category\ValueObject\CategoryId;
use Symfony\Contracts\EventDispatcher\Event;

final class CategoryUpdatedEvent extends Event
{
    public function __construct(
        private readonly CategoryId $categoryId,
        private readonly string $oldCode,
        private readonly string $newCode,
    ) {}

    public function categoryId(): CategoryId
    {
        return $this->categoryId;
    }

    public function oldCode(): string
    {
        return $this->oldCode;
    }

    public function newCode(): string
    {
        return $this->newCode;
    }
}
