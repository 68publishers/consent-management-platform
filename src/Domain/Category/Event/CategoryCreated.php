<?php

declare(strict_types=1);

namespace App\Domain\Category\Event;

use App\Domain\Category\ValueObject\Code;
use App\Domain\Category\ValueObject\CategoryId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CategoryCreated extends AbstractDomainEvent
{
	private CategoryId $categoryId;

	private Code $code;

	private bool $active;

	private array $names;

	/**
	 * @param \App\Domain\Category\ValueObject\CategoryId $categoryId
	 * @param \App\Domain\Category\ValueObject\Code       $code
	 * @param bool                                        $active
	 * @param array                                       $names
	 *
	 * @return static
	 */
	public static function create(CategoryId $categoryId, Code $code, bool $active, array $names): self
	{
		$event = self::occur($categoryId->toString(), [
			'code' => $code->value(),
			'active' => $active,
			'names' => $names,
		]);

		$event->categoryId = $categoryId;
		$event->code = $code;
		$event->active = $active;
		$event->names = $names;

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
	 * @return \App\Domain\Category\ValueObject\Code
	 */
	public function code(): Code
	{
		return $this->code;
	}

	/**
	 * @return bool
	 */
	public function active(): bool
	{
		return $this->active;
	}

	/**
	 * @return array
	 */
	public function names(): array
	{
		return $this->names;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->categoryId = CategoryId::fromUuid($this->aggregateId()->id());
		$this->code = Code::fromValue($parameters['code']);
		$this->active = (bool) $parameters['active'];
		$this->names = $parameters['names'];
	}
}
