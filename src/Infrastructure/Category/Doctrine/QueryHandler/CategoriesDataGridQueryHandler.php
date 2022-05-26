<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\QueryHandler;

use Doctrine\ORM\QueryBuilder;
use App\Domain\Category\Category;
use App\ReadModel\Category\CategoryView;
use Doctrine\ORM\EntityManagerInterface;
use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\Category\CategoriesDataGridQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class CategoriesDataGridQueryHandler implements QueryHandlerInterface
{
	use DataGridQueryHandlerTrait;

	private EntityManagerInterface $em;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

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
			function (): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('COUNT(c.id)')
					->from(Category::class, 'c')
					->where('c.deletedAt IS NULL');
			},
			function (): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('c, ct')
					->from(Category::class, 'c')
					->leftJoin('c.translations', 'ct')
					->where('c.deletedAt IS NULL');
			},
			static fn (array $data): CategoryView => ViewFactory::createCategoryView($data),
			[
				'code' => ['applyLike', 'c.code'],
				'createdAt' => ['applyDate', 'c.createdAt'],
				'active' => ['applyEquals', 'c.active'],
			],
			[
				'code' => 'c.code',
				'createdAt' => 'c.createdAt',
			]
		);
	}
}
