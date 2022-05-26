<?php

declare(strict_types=1);

namespace App\Infrastructure\Category;

use App\ReadModel\Category\CategoryView;
use App\Domain\Category\ValueObject\Code;
use App\Domain\Category\ValueObject\CategoryId;
use App\ReadModel\Category\GetCategoryByCodeQuery;
use App\Domain\Category\CheckCodeUniquenessInterface;
use App\Domain\Category\Exception\CodeUniquenessException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class CheckCodeUniqueness implements CheckCodeUniquenessInterface
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
	public function __invoke(CategoryId $categoryId, Code $code): void
	{
		$categoryView = $this->queryBus->dispatch(GetCategoryByCodeQuery::create($code->value()));

		if (!$categoryView instanceof CategoryView) {
			return;
		}

		if (!$categoryView->id->equals($categoryId)) {
			throw CodeUniquenessException::create($code->value());
		}
	}
}
