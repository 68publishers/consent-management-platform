<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\ReadModel;

use App\Domain\Project\Project;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Project\ProjectExistsQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class ProjectExistsQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Project\ProjectExistsQuery $query
	 *
	 * @return \App\Domain\Project\ValueObject\ProjectId|false
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(ProjectExistsQuery $query)
	{
		$qb = $this->em->createQueryBuilder()
			->select('p.id')
			->from(Project::class, 'p')
			->where('p.deletedAt IS NULL');

		if (NULL !== $query->projectId()) {
			$qb->andWhere('p.id = :projectId')
				->setParameter('projectId', $query->projectId());
		}

		if (NULL !== $query->code()) {
			$qb->andWhere('p.code = :code')
				->setParameter('code', $query->code());
		}

		try {
			return $qb->getQuery()->getSingleResult()['id'];
		} catch (NoResultException $e) {
			return FALSE;
		}
	}
}
