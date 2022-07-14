<?php

declare(strict_types=1);

namespace App\ReadModel\Category;

use DateTimeImmutable;
use DateTimeInterface;
use App\Domain\Category\ValueObject\Code;
use App\Domain\Category\ValueObject\Name;
use App\Domain\Category\ValueObject\CategoryId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class CategoryView extends AbstractView
{
	public CategoryId $id;

	public DateTimeImmutable $createdAt;

	public ?DateTimeImmutable $deletedAt = NULL;

	public Code $code;

	public bool $active;

	public bool $necessary;

	/** @var Name[] */
	public array $names;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id->toString(),
			'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
			'deletedAt' => NULL !== $this->deletedAt ? $this->deletedAt->format(DateTimeInterface::ATOM) : NULL,
			'code' => $this->code->value(),
			'active' => $this->active,
			'necessary' => $this->necessary,
			'names' => array_map(static fn (Name $name): string => $name->value(), $this->names),
		];
	}
}
