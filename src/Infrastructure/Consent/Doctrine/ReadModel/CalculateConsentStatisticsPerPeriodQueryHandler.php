<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\ReadModel;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
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
	 * @return \App\ReadModel\Consent\ConsentStatisticsView
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(CalculateConsentStatisticsPerPeriodQuery $query): ConsentStatisticsView
	{
		$totalStatisticsQuery = '
		SELECT
			count(*) AS "totalConsentsCount",
			coalesce(sum(sp.positive_count), 0) AS "totalPositiveCount",
			coalesce(sum(sp.negative_count), 0) AS "totalNegativeCount"
		FROM consent_statistics_projection sp
		WHERE sp.project_id = :projectId AND sp.created_at BETWEEN :startDate AND :endDate
		';

		$rsm = new ResultSetMappingBuilder($this->em);
		$rsm->addScalarResult('totalConsentsCount', 'totalConsentsCount', 'integer');
		$rsm->addScalarResult('totalPositiveCount', 'totalPositiveCount', 'integer');
		$rsm->addScalarResult('totalNegativeCount', 'totalNegativeCount', 'integer');

		$totalData = $this->em->createNativeQuery($totalStatisticsQuery, $rsm)
			->setParameters([
				'projectId' => $query->projectId(),
				'startDate' => $query->startDate(),
				'endDate' => $query->endDate(),
			])
			->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

		$uniqueStatisticsQuery = '
 		SELECT
			count(*) AS "uniqueConsentsCount",
			coalesce(sum(sp.positive_count), 0) AS "uniquePositiveCount",
		  	coalesce(sum(sp.negative_count), 0) AS "uniqueNegativeCount"
		FROM (
  			SELECT DISTINCT ON (_sp.consent_id) _sp.positive_count, _sp.negative_count
  			FROM consent_statistics_projection _sp
  			WHERE _sp.project_id = :projectId AND _sp.created_at BETWEEN :startDate AND :endDate
  			ORDER BY _sp.consent_id, _sp.created_at DESC
		) sp
		';

		$rsm = new ResultSetMappingBuilder($this->em);
		$rsm->addScalarResult('uniqueConsentsCount', 'uniqueConsentsCount', 'integer');
		$rsm->addScalarResult('uniquePositiveCount', 'uniquePositiveCount', 'integer');
		$rsm->addScalarResult('uniqueNegativeCount', 'uniqueNegativeCount', 'integer');

		$uniqueData = $this->em->createNativeQuery($uniqueStatisticsQuery, $rsm)
			->setParameters([
				'projectId' => $query->projectId(),
				'startDate' => $query->startDate(),
				'endDate' => $query->endDate(),
			])
			->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

		$data = array_merge($totalData, $uniqueData);

		return $this->viewFactory->create(ConsentStatisticsView::class, DoctrineViewData::create($data));
	}
}
