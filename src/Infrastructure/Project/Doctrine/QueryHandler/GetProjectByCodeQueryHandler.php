<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\QueryHandler;

use App\Domain\Project\Project;
use Doctrine\ORM\AbstractQuery;
use App\ReadModel\Project\ProjectView;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Project\GetProjectByCodeQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class GetProjectByCodeQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Project\GetProjectByCodeQuery $query
	 *
	 * @return \App\ReadModel\Project\ProjectView|NULL
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetProjectByCodeQuery $query): ?ProjectView
	{
		$data = $this->em->createQueryBuilder()
			->select('p')
			->from(Project::class, 'p')
			->where('p.code = :code')
			->setParameter('code', $query->code())
			->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return NULL !== $data ? ProjectView::fromArray($data) : NULL;
	}
}
