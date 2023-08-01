<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryForm\Event;

use App\Domain\Category\ValueObject\CategoryId;
use Symfony\Contracts\EventDispatcher\Event;

final class CategoryUpdatedEvent extends Event
{
    private CategoryId $categoryId;

    private string $oldCode;

    private string $newCode;

    public function __construct(CategoryId $categoryId, string $oldCode, string $newCode)
    {
        $this->categoryId = $categoryId;
        $this->oldCode = $oldCode;
        $this->newCode = $newCode;
    }

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
