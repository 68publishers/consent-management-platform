<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\ReadModel;

use App\Domain\Project\Project;
use Doctrine\ORM\AbstractQuery;
use App\Domain\User\UserHasProject;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Project\ProjectAccessibilityView;
use App\ReadModel\Project\FindProjectsAccessibilityByCodeQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class FindProjectsAccessibilityByCodeQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Project\FindProjectsAccessibilityByCodeQuery $query
	 *
	 * @return \App\ReadModel\Project\ProjectAccessibilityView[]
	 */
	public function __invoke(FindProjectsAccessibilityByCodeQuery $query): array
	{
		$accessibilitySubQuery = $this->em->createQueryBuilder()
			->select('1')
			->from(UserHasProject::class, 'uhp')
			->where('uhp.projectId = p.id AND uhp.user = :userId')
			->getQuery()
			->getDQL();

		$data = $this->em->createQueryBuilder()
			->select('p.id AS projectId, p.code AS projectCode')
			->addSelect(sprintf(
				'CASE WHEN (%s) = 1 THEN true ELSE false END AS accessible',
				$accessibilitySubQuery
			))
			->from(Project::class, 'p')
			->where('p.deletedAt IS NULL AND p.code IN (:projectCodes)')
			->orderBy('p.createdAt', 'DESC')
			->setParameters([
				'userId' => $query->userId(),
				'projectCodes' => $query->projectCodes(),
			])
			->getQuery()
			->getResult(AbstractQuery::HYDRATE_ARRAY);

		return array_map(fn (array $row): ProjectAccessibilityView => $this->viewFactory->create(ProjectAccessibilityView::class, DoctrineViewData::create($row)), $data);
	}
}
