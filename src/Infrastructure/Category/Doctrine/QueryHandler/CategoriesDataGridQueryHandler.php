<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\QueryHandler;

use Doctrine\ORM\QueryBuilder;
use App\Domain\Category\Category;
use Doctrine\ORM\Query\Expr\Join;
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
			function (CategoriesDataGridQuery $query): QueryBuilder {
				$qb = $this->em->createQueryBuilder()
					->select('COUNT(c.id)')
					->from(Category::class, 'c')
					->where('c.deletedAt IS NULL');

				if (NULL !== $query->locale()) {
					$qb->leftJoin('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
						->setParameter('locale', $query->locale());
				}

				return $qb;
			},
			function (CategoriesDataGridQuery $query): QueryBuilder {
				$qb = $this->em->createQueryBuilder()
					->select('c')
					->from(Category::class, 'c')
					->where('c.deletedAt IS NULL');

				if (NULL !== $query->locale()) {
					$qb->addSelect('ct')
						->leftJoin('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
						->setParameter('locale', $query->locale());
				}

				return $qb;
			},
			static fn (array $data): CategoryView => ViewFactory::createCategoryView($data),
			[
				'name' => ['applyFilterName', 'ct.name', [$query->locale()]],
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

	/**
	 * @param \Doctrine\ORM\QueryBuilder $qb
	 * @param string                     $column
	 * @param mixed                      $value
	 * @param string|NULL                $locale
	 *
	 * @return void
	 */
	protected function applyFilterName(QueryBuilder $qb, string $column, $value, ?string $locale): void
	{
		if (NULL !== $locale) {
			$this->applyLike($qb, $column, $value);
		}
	}
}
