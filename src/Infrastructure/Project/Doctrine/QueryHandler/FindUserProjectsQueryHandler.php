<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\QueryHandler;

use App\Domain\Project\Project;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use App\Domain\User\UserHasProject;
use App\ReadModel\Project\ProjectView;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Project\FindUserProjectsQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class FindUserProjectsQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Project\FindUserProjectsQuery $query
	 *
	 * @return \App\ReadModel\Project\ProjectView[]
	 */
	public function __invoke(FindUserProjectsQuery $query): array
	{
		$data = $this->em->createQueryBuilder()
			->select('p')
			->from(Project::class, 'p')
			->join(UserHasProject::class, 'uhp', Join::WITH, 'uhp.projectId = p.id AND uhp.user = :userId')
			->orderBy('p.createdAt', 'DESC')
			->setParameter('userId', $query->userId())
			->getQuery()
			->getResult(AbstractQuery::HYDRATE_ARRAY);

		return array_map(static fn (array $row): ProjectView => ViewFactory::createProjectView($row), $data);
	}
}
