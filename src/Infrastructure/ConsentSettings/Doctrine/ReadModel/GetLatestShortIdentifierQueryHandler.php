<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine\ReadModel;

use App\Domain\Project\Project;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\ConsentSettings\ConsentSettings;
use App\ReadModel\ConsentSettings\GetLatestShortIdentifierQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class GetLatestShortIdentifierQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\ConsentSettings\GetLatestShortIdentifierQuery $query
	 *
	 * @return int
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetLatestShortIdentifierQuery $query): int
	{
		try {
			return (int) $this->em->createQueryBuilder()
				->select('MAX(cs.shortIdentifier)')
				->from(ConsentSettings::class, 'cs')
				->join(Project::class, 'p', Join::WITH, 'cs.projectId = p.id AND p.id = :projectId AND p.deletedAt IS NULL')
				->setParameters([
					'projectId' => $query->projectId(),
				])
				->getQuery()
				->getSingleScalarResult();
		} catch (NoResultException $e) {
			return 0;
		}
	}
}
