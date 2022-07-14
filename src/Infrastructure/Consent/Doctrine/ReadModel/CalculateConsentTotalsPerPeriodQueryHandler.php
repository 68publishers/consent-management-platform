<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\ReadModel;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Consent\Event\ConsentCreated;
use App\Domain\Consent\Event\ConsentUpdated;
use App\ReadModel\Consent\ConsentTotalsView;
use App\Domain\Project\ValueObject\ProjectId;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use App\ReadModel\Consent\CalculateConsentTotalsPerPeriodQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class CalculateConsentTotalsPerPeriodQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Consent\CalculateConsentTotalsPerPeriodQuery $query
	 *
	 * @return \App\ReadModel\Consent\ConsentTotalsView[]
	 */
	public function __invoke(CalculateConsentTotalsPerPeriodQuery $query): array
	{
		$sql = "
		SELECT c.project_id, COUNT(DISTINCT es.id) AS total, COUNT(DISTINCT c.user_identifier) AS \"unique\"
		FROM consent_event_stream es
		JOIN consent c ON c.id = es.aggregate_id
			AND c.project_id IN (:projectIds)
			AND es.event_name IN (:eventNames)
		WHERE es.created_at BETWEEN :startDate AND :endDate
		GROUP BY c.project_id;
		";

		$rsm = new ResultSetMappingBuilder($this->em);
		$rsm->addScalarResult('project_id', 'projectId', ProjectId::class);
		$rsm->addScalarResult('total', 'total', 'integer');
		$rsm->addScalarResult('unique', 'unique', 'integer');

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

		return array_map(fn (array $row): ConsentTotalsView => $this->viewFactory->create(ConsentTotalsView::class, DoctrineViewData::create($row)), $data);
	}
}
