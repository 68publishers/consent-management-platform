<?php

declare(strict_types=1);

namespace App\Domain\Category\Event;

use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Category\ValueObject\Code;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CategoryCodeChanged extends AbstractDomainEvent
{
    private CategoryId $categoryId;

    private Code $code;

    /**
     * @return static
     */
    public static function create(CategoryId $categoryId, Code $code): self
    {
        $event = self::occur($categoryId->toString(), [
            'code' => $code->value(),
        ]);

        $event->categoryId = $categoryId;
        $event->code = $code;

        return $event;
    }

    public function categoryId(): CategoryId
    {
        return $this->categoryId;
    }

    public function code(): Code
    {
        return $this->code;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->categoryId = CategoryId::fromUuid($this->aggregateId()->id());
        $this->code = Code::fromValue($parameters['code']);
    }
}
