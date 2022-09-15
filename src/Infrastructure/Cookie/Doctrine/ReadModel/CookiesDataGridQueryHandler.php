<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\ReadModel;

use App\Domain\Cookie\Cookie;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Project\Project;
use App\Domain\Category\Category;
use Doctrine\ORM\Query\Expr\Join;
use App\Domain\CookieProvider\CookieProvider;
use App\ReadModel\Cookie\CookiesDataGridQuery;
use App\Domain\Project\ProjectHasCookieProvider;
use App\ReadModel\Cookie\CookieDataGridItemView;
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
				$qb = $this->em->createQueryBuilder()
					->select('COUNT(c.id)')
					->from(Cookie::class, 'c')
					->leftJoin(Category::class, 'cat', Join::WITH, 'cat.id = c.categoryId AND cat.deletedAt IS NULL')
					->join(CookieProvider::class, 'cp', Join::WITH, 'cp.id = c.cookieProviderId AND cp.deletedAt IS NULL')
					->where('c.deletedAt IS NULL');

				if (NULL !== $query->cookieProviderId()) {
					$qb->andWhere('cp.id = :cookieProviderId')
						->setParameter('cookieProviderId', $query->cookieProviderId());
				}

				if (NULL !== $query->projectId()) {
					$qb->leftJoin(ProjectHasCookieProvider::class, 'phcp', Join::WITH, 'phcp.cookieProviderId = cp.id')
						->leftJoin('phcp.project', 'phcp_p', Join::WITH, 'phcp_p.deletedAt IS NULL');

					if (FALSE === $query->projectServicesOnly()) {
						$qb->leftJoin(Project::class, 'p', Join::WITH, 'p.cookieProviderId = cp.id AND p.deletedAt IS NULL');
					}

					$qb->andWhere($query->projectServicesOnly() ? 'phcp_p.id = :projectId' : 'phcp_p.id = :projectId OR p.id = :projectId')
						->setParameter('projectId', $query->projectId());
				}

				return $qb;
			},
			function (CookiesDataGridQuery $query): QueryBuilder {
				$qb = $this->em->createQueryBuilder()
					->select('c.id AS id, cat.id AS categoryId, c.name AS cookieName, c.processingTime AS processingTime, c.active AS active, cat_t.name AS categoryName, c.createdAt AS createdAt')
					->addSelect('cp.id AS cookieProviderId, cp.name AS cookieProviderName, cp.type AS cookieProviderType, cp.private AS cookieProviderPrivate')
					->from(Cookie::class, 'c')
					->leftJoin(Category::class, 'cat', Join::WITH, 'cat.id = c.categoryId AND cat.deletedAt IS NULL')
					->leftJoin('cat.translations', 'cat_t', Join::WITH, 'cat_t.locale = :locale')
					->join(CookieProvider::class, 'cp', Join::WITH, 'cp.id = c.cookieProviderId AND cp.deletedAt IS NULL')
					->where('c.deletedAt IS NULL')
					->setParameters([
						'locale' => $query->locale() ?? '_unknown_',
					]);

				if (NULL !== $query->cookieProviderId()) {
					$qb->andWhere('cp.id = :cookieProviderId')
						->setParameter('cookieProviderId', $query->cookieProviderId());
				}

				if (NULL !== $query->projectId()) {
					$qb->leftJoin(ProjectHasCookieProvider::class, 'phcp', Join::WITH, 'phcp.cookieProviderId = cp.id')
						->leftJoin('phcp.project', 'phcp_p', Join::WITH, 'phcp_p.deletedAt IS NULL');

					if (FALSE === $query->projectServicesOnly()) {
						$qb->leftJoin(Project::class, 'p', Join::WITH, 'p.cookieProviderId = cp.id AND p.deletedAt IS NULL');
					}

					$qb->andWhere($query->projectServicesOnly() ? 'phcp_p.id = :projectId' : 'phcp_p.id = :projectId OR p.id = :projectId')
						->setParameter('projectId', $query->projectId());
				}

				if ($query->includeProjectsData()) {
					$projectsSubQuery = $this->em->createQueryBuilder()
						->select('JSON_AGG(DISTINCT __p.name ORDER BY __p.name)')
						->from(Project::class, '__p')
						->leftJoin('__p.cookieProviders', '__phcp', Join::WITH)
						->where('__p.deletedAt IS NULL')
						->andWhere('__p.cookieProviderId = cp.id OR __phcp.cookieProviderId = cp.id')
						->getQuery()
						->getDQL();

					$qb->addSelect(sprintf(
						'(%s) AS projects',
						$projectsSubQuery
					));
				}

				return $qb;
			},
			CookieDataGridItemView::class,
			[
				'id' => ['applyEquals', 'c.id'],
				'cookieName' => ['applyLike', 'c.name'],
				'categoryId' => ['applyIn', 'cat.id'],
				'providerId' => ['applyIn', 'cp.id'],
				'providerType' => ['applyEquals', 'cp.type'],
				'createdAt' => ['applyDate', 'c.createdAt'],
				'active' => ['applyEquals', 'c.active'],
				'projects' => ['applyProjects', '_'],
			],
			[
				'cookieName' => 'c.name',
				'categoryName' => 'cat_t.name',
				'providerName' => 'cp.name',
				'createdAt' => 'c.createdAt',
			]
		);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $qb
	 * @param string                     $_
	 * @param mixed                      $value
	 *
	 * @return void
	 */
	private function applyProjects(QueryBuilder $qb, string $_, $value): void
	{
		$value = (array) $value;

		if (empty($value)) {
			return;
		}

		$paramName = $this->newParameterName();

		$qb->leftJoin(ProjectHasCookieProvider::class, 'phcp', Join::WITH, 'phcp.cookieProviderId = cp.id')
			->leftJoin(Project::class, 'p_private', Join::WITH, 'p_private.cookieProviderId = cp.id')
			->andWhere(sprintf(
				'p_private.id IN (:%s) OR phcp.project IN (:%s)',
				$paramName,
				$paramName
			))
			->setParameter($paramName, $value);
	}
}
