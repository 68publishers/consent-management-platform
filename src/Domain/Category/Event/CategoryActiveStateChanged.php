<?php

declare(strict_types=1);

namespace App\Domain\Category\Event;

use App\Domain\Category\ValueObject\CategoryId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CategoryActiveStateChanged extends AbstractDomainEvent
{
    private CategoryId $categoryId;

    private bool $active;

    public static function create(CategoryId $categoryId, bool $active): self
    {
        $event = self::occur($categoryId->toString(), [
            'active' => $active,
        ]);

        $event->categoryId = $categoryId;
        $event->active = $active;

        return $event;
    }

    public function categoryId(): CategoryId
    {
        return $this->categoryId;
    }

    public function active(): bool
    {
        return $this->active;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->categoryId = CategoryId::fromUuid($this->aggregateId()->id());
        $this->active = (bool) $parameters['active'];
    }
}
