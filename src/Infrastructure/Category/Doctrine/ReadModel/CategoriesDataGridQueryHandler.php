<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\ReadModel;

use Doctrine\ORM\QueryBuilder;
use App\Domain\Category\Category;
use Doctrine\ORM\Query\Expr\Join;
use App\ReadModel\Category\CategoryView;
use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\Category\CategoriesDataGridQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class CategoriesDataGridQueryHandler implements QueryHandlerInterface
{
	use DataGridQueryHandlerTrait;

	/**
	 * @param \App\ReadModel\Category\CategoriesDataGridQuery $query
	 *
	 * @return array|int
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(CategoriesDataGridQuery $query)
	{
		return $this->processQuery(
			$query,
			function (CategoriesDataGridQuery $query): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('COUNT(c.id)')
					->from(Category::class, 'c')
					->leftJoin('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
					->where('c.deletedAt IS NULL')
					->setParameter('locale', $query->locale() ?? '_unknown_');
			},
			function (CategoriesDataGridQuery $query): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('c, ct')
					->from(Category::class, 'c')
					->leftJoin('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
					->where('c.deletedAt IS NULL')
					->setParameter('locale', $query->locale() ?? '_unknown_');
			},
			CategoryView::class,
			[
				'name' => ['applyLike', 'ct.name'],
				'code' => ['applyLike', 'c.code'],
				'createdAt' => ['applyDate', 'c.createdAt'],
				'active' => ['applyEquals', 'c.active'],
			],
			[
				'name' => 'ct.name',
				'code' => 'c.code',
				'createdAt' => 'c.createdAt',
			]
		);
	}
}
