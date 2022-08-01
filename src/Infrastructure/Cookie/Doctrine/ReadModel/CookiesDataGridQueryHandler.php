<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\ReadModel;

use App\Domain\Cookie\Cookie;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Category\Category;
use Doctrine\ORM\Query\Expr\Join;
use App\ReadModel\Cookie\CookieItemView;
use App\ReadModel\Cookie\CookiesDataGridQuery;
use App\Infrastructure\DataGridQueryHandlerTrait;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class CookiesDataGridQueryHandler implements QueryHandlerInterface
{
	use DataGridQueryHandlerTrait;

	/**
	 * @param \App\ReadModel\Cookie\CookiesDataGridQuery $query
	 *
	 * @return array|int
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(CookiesDataGridQuery $query)
	{
		return $this->processQuery(
			$query,
			function (CookiesDataGridQuery $query): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('COUNT(c.id)')
					->from(Cookie::class, 'c')
					->leftJoin(Category::class, 'cat', Join::WITH, 'cat.id = c.categoryId AND cat.deletedAt IS NULL')
					->where('c.deletedAt IS NULL')
					->andWhere('c.cookieProviderId = :cookieProviderId')
					->setParameters([
						'cookieProviderId' => $query->cookieProviderId(),
					]);
			},
			function (CookiesDataGridQuery $query): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('c.id AS id, cat.id AS categoryId, c.name AS cookieName, c.processingTime AS processingTime, c.active AS active, cat_t.name AS categoryName, c.createdAt AS createdAt')
					->from(Cookie::class, 'c')
					->leftJoin(Category::class, 'cat', Join::WITH, 'cat.id = c.categoryId AND cat.deletedAt IS NULL')
					->leftJoin('cat.translations', 'cat_t', Join::WITH, 'cat_t.locale = :locale')
					->where('c.deletedAt IS NULL')
					->andWhere('c.cookieProviderId = :cookieProviderId')
					->setParameters([
						'locale' => $query->locale() ?? '_unknown_',
						'cookieProviderId' => $query->cookieProviderId(),
					]);
			},
			CookieItemView::class,
			[
				'id' => ['applyEquals', 'c.id'],
				'cookieName' => ['applyLike', 'c.name'],
				'categoryName' => ['applyIn', 'cat.id'],
				'createdAt' => ['applyDate', 'c.createdAt'],
				'active' => ['applyEquals', 'c.active'],
			],
			[
				'cookieName' => 'c.name',
				'categoryName' => 'cat_t.name',
				'createdAt' => 'c.createdAt',
			]
		);
	}
}
