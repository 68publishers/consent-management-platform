<?php

declare(strict_types=1);

namespace App\Domain\Category\Event;

use App\Domain\Category\ValueObject\CategoryId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CategoryNecessaryChanged extends AbstractDomainEvent
{
    private CategoryId $categoryId;

    private bool $necessary;

    public static function create(CategoryId $categoryId, bool $necessary): self
    {
        $event = self::occur($categoryId->toString(), [
            'necessary' => $necessary,
        ]);

        $event->categoryId = $categoryId;
        $event->necessary = $necessary;

        return $event;
    }

    public function categoryId(): CategoryId
    {
        return $this->categoryId;
    }

    public function necessary(): bool
    {
        return $this->necessary;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->categoryId = CategoryId::fromUuid($this->aggregateId()->id());
        $this->necessary = (bool) $parameters['necessary'];
    }
}
