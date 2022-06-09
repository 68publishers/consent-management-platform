<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\ReadModel;

use Doctrine\ORM\QueryBuilder;
use App\Domain\Consent\Consent;
use App\ReadModel\Consent\ConsentView;
use App\ReadModel\Consent\ConsentsDataGridQuery;
use App\Infrastructure\DataGridQueryHandlerTrait;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class ConsentsDataGridQueryHandler implements QueryHandlerInterface
{
	use DataGridQueryHandlerTrait;

	/**
	 * @param \App\ReadModel\Consent\ConsentsDataGridQuery $query
	 *
	 * @return array|int
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(ConsentsDataGridQuery $query)
	{
		return $this->processQuery(
			$query,
			function () use ($query): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('COUNT(c.id)')
					->from(Consent::class, 'c')
					->where('c.projectId = :projectId')
					->setParameter('projectId', $query->projectId());
			},
			function () use ($query): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('c')
					->from(Consent::class, 'c')
					->where('c.projectId = :projectId')
					->setParameter('projectId', $query->projectId());
			},
			ConsentView::class,
			[
				'userIdentifier' => ['applyLike', 'c.userIdentifier'],
				'settingsChecksum' => ['applyLike', 'c.settingsChecksum'],
				'createdAt' => ['applyDate', 'c.createdAt'],
				'lastUpdateAt' => ['applyDate', 'c.lastUpdateAt'],
			],
			[
				'userIdentifier' => 'c.userIdentifier',
				'settingsChecksum' => 'c.settingsChecksum',
				'createdAt' => 'c.createdAt',
				'lastUpdateAt' => 'c.lastUpdateAt',
			]
		);
	}
}
