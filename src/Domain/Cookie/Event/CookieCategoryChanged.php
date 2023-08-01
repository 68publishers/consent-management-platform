<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Cookie\ValueObject\CookieId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieCategoryChanged extends AbstractDomainEvent
{
    private CookieId $cookieId;

    private CategoryId $categoryId;

    public static function create(CookieId $cookieId, CategoryId $categoryId): self
    {
        $event = self::occur($cookieId->toString(), [
            'category_id' => $categoryId->toString(),
        ]);

        $event->cookieId = $cookieId;
        $event->categoryId = $categoryId;

        return $event;
    }

    public function cookieId(): CookieId
    {
        return $this->cookieId;
    }

    public function categoryId(): CategoryId
    {
        return $this->categoryId;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->cookieId = CookieId::fromUuid($this->aggregateId()->id());
        $this->categoryId = CategoryId::fromString($parameters['category_id']);
    }
}
