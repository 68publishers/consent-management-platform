<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryForm\Event;

use App\Domain\Category\ValueObject\CategoryId;
use Symfony\Contracts\EventDispatcher\Event;

final class CategoryCreatedEvent extends Event
{
    private CategoryId $categoryId;

    private string $code;

    public function __construct(CategoryId $categoryId, string $code)
    {
        $this->categoryId = $categoryId;
        $this->code = $code;
    }

    public function categoryId(): CategoryId
    {
        return $this->categoryId;
    }

    public function code(): string
    {
        return $this->code;
    }
}
