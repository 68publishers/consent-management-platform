<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\ReadModel;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Consent\Event\ConsentCreated;
use App\Domain\Consent\Event\ConsentUpdated;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use App\ReadModel\Consent\ScrollThroughConsentsPerPeriodQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\Batch;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\BatchUtils;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class ScrollThroughConsentsPerPeriodQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Consent\ScrollThroughConsentsPerPeriodQuery $query
	 *
	 * @return iterable
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(ScrollThroughConsentsPerPeriodQuery $query): iterable
	{
		$totalCount = $this->calculateTotalCount($query);

		foreach (BatchUtils::from($totalCount, $query->batchSize()) as [$limit, $offset]) {
			$data = array_map(static fn (array $row) => [
				'projectId' => $row['projectId'],
				'userIdentifier' => $row['userIdentifier'],
				'consents' => $row['parameters']['consents'] ?? [],
			], $this->fetchBatch($query, $limit, $offset));

			yield Batch::create($query->batchSize(), $offset, $limit, $data);
		}
	}

	/**
	 * @param \App\ReadModel\Consent\ScrollThroughConsentsPerPeriodQuery $query
	 * @param int                                                        $limit
	 * @param int                                                        $offset
	 *
	 * @return array
	 */
	private function fetchBatch(ScrollThroughConsentsPerPeriodQuery $query, int $limit, int $offset): array
	{
		$sql = "
		SELECT p.id, c.user_identifier, es.parameters
		FROM consent_event_stream es
		JOIN consent c ON c.id = es.aggregate_id
		JOIN project p ON p.id = c.project_id AND p.id IN (:projectIds) AND p.deleted_at IS NULL
		WHERE es.event_name IN (:eventNames) AND es.created_at BETWEEN :startDate AND :endDate
		ORDER BY es.created_at DESC
		LIMIT :limit OFFSET :offset
		";

		$rsm = new ResultSetMappingBuilder($this->em);
		$rsm->addScalarResult('id', 'projectId', 'string');
		$rsm->addScalarResult('user_identifier', 'userIdentifier', 'string');
		$rsm->addScalarResult('parameters', 'parameters', 'json');

		return $this->em->createNativeQuery($sql, $rsm)
			->setParameters([
				'projectIds' => $query->projectIds(),
				'eventNames' => [
					ConsentCreated::class,
					ConsentUpdated::class,
				],
				'startDate' => $query->startDate(),
				'endDate' => $query->endDate(),
				'limit' => $limit,
				'offset' => $offset,
			])
			->getResult(AbstractQuery::HYDRATE_ARRAY);
	}

	/**
	 * @param \App\ReadModel\Consent\ScrollThroughConsentsPerPeriodQuery $query
	 *
	 * @return int
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	private function calculateTotalCount(ScrollThroughConsentsPerPeriodQuery $query): int
	{
		$sql = "
		SELECT COUNT(es.id) AS cnt
		FROM consent_event_stream es
		JOIN consent c ON c.id = es.aggregate_id
		JOIN project p ON p.id = c.project_id AND p.id IN (:projectIds) AND p.deleted_at IS NULL
		WHERE es.event_name IN (:eventNames) AND es.created_at BETWEEN :startDate AND :endDate
		";

		$rsm = new ResultSetMappingBuilder($this->em);
		$rsm->addScalarResult('cnt', 'cnt', 'integer');

		return $this->em->createNativeQuery($sql, $rsm)
			->setParameters([
				'projectIds' => $query->projectIds(),
				'eventNames' => [
					ConsentCreated::class,
					ConsentUpdated::class,
				],
				'startDate' => $query->startDate(),
				'endDate' => $query->endDate(),
			])
			->getSingleScalarResult();
	}
}
