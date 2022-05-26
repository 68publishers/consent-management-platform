<?php

declare(strict_types=1);

namespace App\Domain\Category\Event;

use App\Domain\Category\ValueObject\CategoryId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CategoryActiveStateChanged extends AbstractDomainEvent
{
	private CategoryId $categoryId;

	private bool $active;

	/**
	 * @param \App\Domain\Category\ValueObject\CategoryId $categoryId
	 * @param bool                                        $active
	 *
	 * @return static
	 */
	public static function create(CategoryId $categoryId, bool $active): self
	{
		$event = self::occur($categoryId->toString(), [
			'active' => $active,
		]);

		$event->categoryId = $categoryId;
		$event->active = $active;

		return $event;
	}

	/**
	 * @return \App\Domain\Category\ValueObject\CategoryId
	 */
	public function categoryId(): CategoryId
	{
		return $this->categoryId;
	}

	/**
	 * @return bool
	 */
	public function active(): bool
	{
		return $this->active;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->categoryId = CategoryId::fromUuid($this->aggregateId()->id());
		$this->active = (bool) $parameters['active'];
	}
}
