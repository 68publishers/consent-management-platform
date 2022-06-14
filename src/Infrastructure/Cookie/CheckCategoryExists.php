<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie;

use App\Domain\Category\ValueObject\CategoryId;
use App\ReadModel\Category\GetCategoryByIdQuery;
use App\Domain\Cookie\CheckCategoryExistsInterface;
use App\Domain\Category\Exception\CategoryNotFoundException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class CheckCategoryExists implements CheckCategoryExistsInterface
{
	private QueryBusInterface $queryBus;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 */
	public function __construct(QueryBusInterface $queryBus)
	{
		$this->queryBus = $queryBus;
	}

	/**
	 * {@inheritDoc}
	 */
	public function __invoke(CategoryId $categoryId): void
	{
		if (NULL === $this->queryBus->dispatch(GetCategoryByIdQuery::create($categoryId->toString()))) {
			throw CategoryNotFoundException::withId($categoryId);
		}
	}
}
