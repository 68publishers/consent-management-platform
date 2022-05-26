<?php

declare(strict_types=1);

namespace App\Domain\Category\Event;

use App\Domain\Category\ValueObject\Name;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Category\ValueObject\CategoryId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CategoryNameUpdated extends AbstractDomainEvent
{
	private CategoryId $categoryId;

	private Locale $locale;

	private Name $name;

	/**
	 * @param \App\Domain\Category\ValueObject\CategoryId $categoryId
	 * @param \App\Domain\Shared\ValueObject\Locale       $locale
	 * @param \App\Domain\Category\ValueObject\Name       $name
	 *
	 * @return static
	 */
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

	/**
	 * @return \App\Domain\Category\ValueObject\CategoryId
	 */
	public function categoryId(): CategoryId
	{
		return $this->categoryId;
	}

	/**
	 * @return \App\Domain\Shared\ValueObject\Locale
	 */
	public function locale(): Locale
	{
		return $this->locale;
	}

	/**
	 * @return \App\Domain\Category\ValueObject\Name
	 */
	public function name(): Name
	{
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->categoryId = CategoryId::fromUuid($this->aggregateId()->id());
		$this->locale = Locale::fromValue($parameters['locale']);
		$this->name = Name::fromValue($parameters['name']);
	}
}
