<?php

declare(strict_types=1);

namespace App\Domain\Category\Event;

use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Category\ValueObject\Name;
use App\Domain\Shared\ValueObject\Locale;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CategoryNameUpdated extends AbstractDomainEvent
{
    private CategoryId $categoryId;

    private Locale $locale;

    private Name $name;

    public static function create(CategoryId $categoryId, Locale $locale, Name $name): self
    {
        $event = self::occur($categoryId->toString(), [
            'locale' => $locale->value(),
            'name' => $name->value(),
        ]);

        $event->categoryId = $categoryId;
        $event->locale = $locale;
        $event->name = $name;

        return $event;
    }

    public function categoryId(): CategoryId
    {
        return $this->categoryId;
    }

    public function locale(): Locale
    {
        return $this->locale;
    }

    public function name(): Name
    {
        return $this->name;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->categoryId = CategoryId::fromUuid($this->aggregateId()->id());
        $this->locale = Locale::fromValue($parameters['locale']);
        $this->name = Name::fromValue($parameters['name']);
    }
}
