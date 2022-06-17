<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\ReadModel;

use App\Domain\Project\Project;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use App\Domain\User\UserHasProject;
use App\ReadModel\Project\ProjectView;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Project\GetUsersProjectByCodeQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class GetUserProjectByCodeQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Project\GetUsersProjectByCodeQuery $query
	 *
	 * @return \App\ReadModel\Project\ProjectView|NULL
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetUsersProjectByCodeQuery $query): ?ProjectView
	{
		$data = $this->em->createQueryBuilder()
			->select('p')
			->from(Project::class, 'p')
			->join(UserHasProject::class, 'uhp', Join::WITH, 'uhp.projectId = p.id AND uhp.user = :userId')
			->where('p.code = :code')
			->andWhere('p.deletedAt IS NULL')
			->setParameters([
				'userId' => $query->userId(),
				'code' => $query->code(),
			])
			->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return NULL !== $data ? $this->viewFactory->create(ProjectView::class, DoctrineViewData::create($data)) : NULL;
	}
}
