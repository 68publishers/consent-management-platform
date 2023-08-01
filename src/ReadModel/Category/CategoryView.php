<?php

declare(strict_types=1);

namespace App\ReadModel\Category;

use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Category\ValueObject\Code;
use App\Domain\Category\ValueObject\Name;
use DateTimeImmutable;
use DateTimeInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class CategoryView extends AbstractView
{
    public CategoryId $id;

    public DateTimeImmutable $createdAt;

    public ?DateTimeImmutable $deletedAt = null;

    public Code $code;

    public bool $active;

    public bool $necessary;

    /** @var array<Name> */
    public array $names;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
            'deletedAt' => $this->deletedAt?->format(DateTimeInterface::ATOM),
            'code' => $this->code->value(),
            'active' => $this->active,
            'necessary' => $this->necessary,
            'names' => array_map(static fn (Name $name): string => $name->value(), $this->names),
        ];
    }
}
