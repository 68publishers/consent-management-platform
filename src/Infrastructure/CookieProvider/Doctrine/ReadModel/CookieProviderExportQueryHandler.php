<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\ReadModel;

use App\Domain\Project\Project;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Domain\CookieProvider\CookieProvider;
use App\ReadModel\CookieProvider\CookieProviderExportQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\Batch;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\BatchUtils;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class CookieProviderExportQueryHandler implements QueryHandlerInterface
{
	private EntityManagerInterface $em;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @param \App\ReadModel\CookieProvider\CookieProviderExportQuery $query
	 *
	 * @return iterable|\SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\Batch[]
	 * @throws \JsonException
	 */
	public function __invoke(CookieProviderExportQuery $query): iterable
	{
		$projectSubQuery = $this->em->createQueryBuilder()
			->select('JSON_AGG(DISTINCT _p.code)')
			->from(Project::class, '_p')
			->leftJoin('_p.cookieProviders', '_chp', Join::WITH, '_chp.cookieProviderId = cp.id')
			->where('_p.deletedAt IS NULL')
			->andWhere('_p.cookieProviderId = cp.id OR _chp.id IS NOT NULL')
			->getQuery();

		$q = $this->em->createQueryBuilder()
			->select('cp AS provider')
			->addSelect('cpt AS translations')
			->addSelect(sprintf(
				'(%s) AS projects',
				$projectSubQuery->getDQL()
			))
			->from(CookieProvider::class, 'cp')
			->leftJoin('cp.translations', 'cpt')
			->where('cp.deletedAt IS NULL')
			->orderBy('cp.name', 'ASC')
			->getQuery()
			->setHydrationMode(AbstractQuery::HYDRATE_ARRAY);

		$paginator = new Paginator($q, TRUE);
		$totalCount = count($paginator);

		foreach (BatchUtils::from($totalCount, $query->batchSize()) as [$limit, $offset]) {
			$paginator->getQuery()
				->setMaxResults($limit)
				->setFirstResult($query->staticOffset() ?? $offset);

			$results = [];

			foreach ($paginator as $item) {
				$results[] = $this->normalize($item);
			}

			yield Batch::create(
				$query->batchSize(),
				$query->staticOffset() ?? $offset,
				$totalCount,
				$results
			);
		}
	}

	/**
	 * @param array $row
	 *
	 * @return array
	 * @throws \JsonException
	 */
	private function normalize(array $row): array
	{
		$item = $row['provider'];
		$item['purpose'] = [];

		foreach ($item['translations'] as $translation) {
			$item['purpose'][$translation['locale']->value()] = $translation['purpose'];
		}

		$item['projects'] = NULL !== $row['projects'] ? json_decode($row['projects'], TRUE, 512, JSON_THROW_ON_ERROR) : [];

		return array_filter(
			$item,
			static fn (string $key): bool => in_array($key, [
				'code',
				'name',
				'type',
				'link',
				'active',
				'projects',
				'purpose',
			], TRUE),
			ARRAY_FILTER_USE_KEY
		);
	}
}
