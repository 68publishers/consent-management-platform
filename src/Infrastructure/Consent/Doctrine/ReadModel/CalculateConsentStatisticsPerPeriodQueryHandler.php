<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\ReadModel;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Consent\Event\ConsentCreated;
use App\Domain\Consent\Event\ConsentUpdated;
use App\Domain\Project\ValueObject\ProjectId;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use App\ReadModel\Consent\ConsentStatisticsView;
use App\ReadModel\Consent\CalculateConsentStatisticsPerPeriodQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class CalculateConsentStatisticsPerPeriodQueryHandler implements QueryHandlerInterface
{
	private EntityManagerInterface $em;

	private ViewFactoryInterface $viewFactory;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface                                         $em
	 * @param \SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface $viewFactory
	 */
	public function __construct(EntityManagerInterface $em, ViewFactoryInterface $viewFactory)
	{
		$this->em = $em;
		$this->viewFactory = $viewFactory;
	}

	/**
	 * @param \App\ReadModel\Consent\CalculateConsentStatisticsPerPeriodQuery $query
	 *
	 * @return array
	 */
	public function __invoke(CalculateConsentStatisticsPerPeriodQuery $query): array
	{
		$categoryPlaceholders = $totalPositiveCountParts = $totalNegativeCountParts = $uniquePositiveCountParts = $uniqueNegativeCountParts = $innerSelectParts = [];

		foreach ($query->categoryCodes() as $categoryCode) {
			$categoryPlaceholders[$categoryCode] = $placeholder = '_cat' . count($categoryPlaceholders);
			$innerSelectParts[] = ", (es.parameters->'consents'->>'$categoryCode')::boolean AS $placeholder";
		}

		foreach ($categoryPlaceholders as $placeholder) {
			$totalPositiveCountParts[] = "count(res.$placeholder) FILTER (WHERE res.$placeholder = TRUE)";
			$totalNegativeCountParts[] = "count(res.$placeholder) FILTER (WHERE res.$placeholder = FALSE)";
			$uniquePositiveCountParts[] = "count(res.$placeholder) FILTER (WHERE res.$placeholder = TRUE AND res.seq = 0)";
			$uniqueNegativeCountParts[] = "count(res.$placeholder) FILTER (WHERE res.$placeholder = FALSE AND res.seq = 0)";
		}

		$totalPositiveCountField = empty($totalPositiveCountParts) ? '0' : implode(' + ', $totalPositiveCountParts);
		$totalNegativeCountField = empty($totalNegativeCountParts) ? '0' : implode(' + ', $totalNegativeCountParts);
		$uniquePositiveCountField = empty($uniquePositiveCountParts) ? '0' : implode(' + ', $uniquePositiveCountParts);
		$uniqueNegativeCountField = empty($uniqueNegativeCountParts) ? '0' : implode(' + ', $uniqueNegativeCountParts);
		$innerSelect = implode('', $innerSelectParts);

		$sql = "
		SELECT
			res.project_id AS \"projectId\",
			count(res.event_id) AS \"totalConsentsCount\",
			count(res.event_id) FILTER (WHERE res.seq = 0) AS \"uniqueConsentsCount\",
			$totalPositiveCountField AS \"totalPositiveCount\",
			$totalNegativeCountField AS \"totalNegativeCount\",
			$uniquePositiveCountField AS \"uniquePositiveCount\",
			$uniqueNegativeCountField AS \"uniqueNegativeCount\"
		FROM
			(SELECT
				es.id AS event_id,
				c.project_id AS project_id,
				row_number() OVER (PARTITION BY c.user_identifier, c.project_id ORDER BY es.id DESC) - 1 AS seq
				$innerSelect
			FROM consent_event_stream es
			JOIN consent c ON c.id = es.aggregate_id
			JOIN project p ON p.id = c.project_id AND p.id IN (:projectIds) AND p.deleted_at IS NULL
			WHERE
				es.event_name IN (:eventNames)
				AND es.created_at BETWEEN :startDate AND :endDate
			) res
		GROUP BY res.project_id
		";

		$rsm = new ResultSetMappingBuilder($this->em);
		$rsm->addScalarResult('projectId', 'projectId', ProjectId::class);
		$rsm->addScalarResult('totalConsentsCount', 'totalConsentsCount', 'integer');
		$rsm->addScalarResult('uniqueConsentsCount', 'uniqueConsentsCount', 'integer');
		$rsm->addScalarResult('totalPositiveCount', 'totalPositiveCount', 'integer');
		$rsm->addScalarResult('totalNegativeCount', 'totalNegativeCount', 'integer');
		$rsm->addScalarResult('uniquePositiveCount', 'uniquePositiveCount', 'integer');
		$rsm->addScalarResult('uniqueNegativeCount', 'uniqueNegativeCount', 'integer');

		$data = $this->em->createNativeQuery($sql, $rsm)
			->setParameters([
				'projectIds' => $query->projectIds(),
				'eventNames' => [
					ConsentCreated::class,
					ConsentUpdated::class,
				],
				'startDate' => $query->startDate(),
				'endDate' => $query->endDate(),
			])
			->getResult(AbstractQuery::HYDRATE_ARRAY);

		return array_map(fn (array $row): ConsentStatisticsView => $this->viewFactory->create(ConsentStatisticsView::class, DoctrineViewData::create($row)), $data);
	}
}
