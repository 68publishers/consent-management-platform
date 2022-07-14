<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\ReadModel;

use App\Domain\Consent\Consent;
use App\Domain\Project\Project;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Consent\LastConsentDateView;
use App\ReadModel\Consent\CalculateLastConsentDatesQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class CalculateLastConsentDatesQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Consent\CalculateLastConsentDatesQuery $query
	 *
	 * @return \App\ReadModel\Consent\LastConsentDateView[]
	 */
	public function __invoke(CalculateLastConsentDatesQuery $query): array
	{
		$data = $this->em->createQueryBuilder()
			->select('p.id AS projectId, MAX(c.lastUpdateAt) AS lastConsentDate')
			->from(Project::class, 'p')
			->leftJoin(Consent::class, 'c', Join::WITH, 'c.projectId = p.id')
			->where('p.id IN (:projectIds) AND p.deletedAt IS NULL')
			->groupBy('p.id')
			->setParameters([
				'projectIds' => $query->projectIds(),
			])
			->getQuery()
			->getResult(AbstractQuery::HYDRATE_ARRAY);

		return array_map(fn (array $row): LastConsentDateView => $this->viewFactory->create(LastConsentDateView::class, DoctrineViewData::create($row)), $data);
	}
}
