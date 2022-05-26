<?php

declare(strict_types=1);

namespace App\Infrastructure\Category;

use App\Domain\Category\Category;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Category\CategoryRepositoryInterface;
use App\Domain\Category\Exception\CategoryNotFoundException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Common\Repository\AggregateRootRepositoryInterface;

final class CategoryRepository implements CategoryRepositoryInterface
{
	private AggregateRootRepositoryInterface $aggregateRootRepository;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Infrastructure\Common\Repository\AggregateRootRepositoryInterface $aggregateRootRepository
	 */
	public function __construct(AggregateRootRepositoryInterface $aggregateRootRepository)
	{
		$this->aggregateRootRepository = $aggregateRootRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	public function save(Category $category): void
	{
		$this->aggregateRootRepository->saveAggregateRoot($category);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(CategoryId $id): Category
	{
		$category = $this->aggregateRootRepository->loadAggregateRoot(Category::class, AggregateId::fromUuid($id->id()));

		if (!$category instanceof Category) {
			throw CategoryNotFoundException::withId($id);
		}

		return $category;
	}
}
