<?php

declare(strict_types=1);

namespace App\Domain\Category\Event;

use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Category\ValueObject\Code;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CategoryCreated extends AbstractDomainEvent
{
    private CategoryId $categoryId;

    private Code $code;

    private bool $active;

    private bool $necessary;

    private array $names;

    public static function create(CategoryId $categoryId, Code $code, bool $active, bool $necessary, array $names): self
    {
        $event = self::occur($categoryId->toString(), [
            'code' => $code->value(),
            'active' => $active,
            'necessary' => $necessary,
            'names' => $names,
        ]);

        $event->categoryId = $categoryId;
        $event->code = $code;
        $event->active = $active;
        $event->necessary = $necessary;
        $event->names = $names;

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

    public function active(): bool
    {
        return $this->active;
    }

    public function necessary(): bool
    {
        return $this->necessary;
    }

    public function names(): array
    {
        return $this->names;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->categoryId = CategoryId::fromUuid($this->aggregateId()->id());
        $this->code = Code::fromValue($parameters['code']);
        $this->active = (bool) $parameters['active'];
        $this->necessary = (bool) $parameters['necessary'];
        $this->names = $parameters['names'];
    }
}
