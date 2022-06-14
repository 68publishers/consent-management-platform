<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Category\ValueObject\CategoryId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieCategoryChanged extends AbstractDomainEvent
{
	private CookieId $cookieId;

	private CategoryId $categoryId;

	/**
	 * @param \App\Domain\Cookie\ValueObject\CookieId     $cookieId
	 * @param \App\Domain\Category\ValueObject\CategoryId $categoryId
	 *
	 * @return static
	 */
	public static function create(CookieId $cookieId, CategoryId $categoryId): self
	{
		$event = self::occur($cookieId->toString(), [
			'category_id' => $categoryId->toString(),
		]);

		$event->cookieId = $cookieId;
		$event->categoryId = $categoryId;

		return $event;
	}

	/**
	 * @return \App\Domain\Cookie\ValueObject\CookieId
	 */
	public function cookieId(): CookieId
	{
		return $this->cookieId;
	}

	/**
	 * @return \App\Domain\Category\ValueObject\CategoryId
	 */
	public function categoryId(): CategoryId
	{
		return $this->categoryId;
	}

	/**
	 * @param array $parameters
	 *
	 * @return void
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->cookieId = CookieId::fromUuid($this->aggregateId()->id());
		$this->categoryId = CategoryId::fromString($parameters['category_id']);
	}
}
