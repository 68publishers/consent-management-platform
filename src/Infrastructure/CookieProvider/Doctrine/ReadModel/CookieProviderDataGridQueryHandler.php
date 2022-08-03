<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\ReadModel;

use App\Domain\Cookie\Cookie;
use Doctrine\ORM\QueryBuilder;
use App\Domain\CookieProvider\CookieProvider;
use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\CookieProvider\CookieProvidersDataGridQuery;
use App\ReadModel\CookieProvider\CookieProviderDaraGridItemView;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class CookieProviderDataGridQueryHandler implements QueryHandlerInterface
{
	use DataGridQueryHandlerTrait;

	/**
	 * @param \App\ReadModel\CookieProvider\CookieProvidersDataGridQuery $query
	 *
	 * @return array|int
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(CookieProvidersDataGridQuery $query)
	{
		return $this->processQuery(
			$query,
			function (): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('COUNT(c.id)')
					->from(CookieProvider::class, 'c')
					->where('c.deletedAt IS NULL')
					->andWhere('c.private = false');
			},
			function (): QueryBuilder {
				$numberOfCookiesSubQuery = $this->em->createQueryBuilder()
					->select('COUNT(cookie.id)')
					->from(Cookie::class, 'cookie')
					->where('cookie.cookieProviderId = c.id')
					->andWhere('cookie.deletedAt IS NULL')
					->getQuery()
					->getDQL();

				return $this->em->createQueryBuilder()
					->select('c.id, c.createdAt, c.code, c.type, c.name, c.link, c.private, c.active')
					->addSelect(sprintf(
						'(%s) AS numberOfCookies',
						$numberOfCookiesSubQuery
					))
					->from(CookieProvider::class, 'c')
					->where('c.deletedAt IS NULL')
					->andWhere('c.private = false');
			},
			CookieProviderDaraGridItemView::class,
			[
				'name' => ['applyLike', 'c.name'],
				'code' => ['applyLike', 'c.code'],
				'link' => ['applyLike', 'c.link'],
				'type' => ['applyEquals', 'c.type'],
				'createdAt' => ['applyDate', 'c.createdAt'],
				'active' => ['applyEquals', 'c.active'],
			],
			[
				'name' => 'c.name',
				'code' => 'c.code',
				'createdAt' => 'c.createdAt',
				'numberOfCookies' => 'numberOfCookies',
			]
		);
	}
}
