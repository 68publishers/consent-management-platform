<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryForm\Event;

use App\Domain\Category\ValueObject\CategoryId;
use Symfony\Contracts\EventDispatcher\Event;

final class CategoryCreatedEvent extends Event
{
    public function __construct(
        private readonly CategoryId $categoryId,
        private readonly string $code,
    ) {}

    public function categoryId(): CategoryId
    {
        return $this->categoryId;
    }

    public function code(): string
    {
        return $this->code;
    }
}
