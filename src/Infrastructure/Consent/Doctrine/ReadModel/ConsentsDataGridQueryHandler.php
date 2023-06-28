<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\ReadModel;

use Doctrine\ORM\QueryBuilder;
use App\Domain\Consent\Consent;
use Doctrine\ORM\Query\Expr\Join;
use App\ReadModel\Consent\ConsentListView;
use App\Domain\ConsentSettings\ConsentSettings;
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
					->select('COUNT_ROWS()')
					->from(Consent::class, 'c')
					->andWhere('c.projectId = :projectId')
					->setParameter('projectId', $query->projectId());
			},
			function () use ($query): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('c.id, c.createdAt, c.lastUpdateAt, c.userIdentifier, c.settingsChecksum, cs.shortIdentifier AS settingsShortIdentifier, cs.id AS settingsId')
					->from(Consent::class, 'c')
					->leftJoin(ConsentSettings::class, 'cs', Join::WITH, 'cs.projectId = c.projectId AND cs.checksum = c.settingsChecksum')
					->andWhere('c.projectId = :projectId')
					->setParameter('projectId', $query->projectId());
			},
			ConsentListView::class,
			[
				'userIdentifier' => ['applyEquals', 'c.userIdentifier'],
				'createdAt' => ['applyDate', 'c.createdAt'],
				'lastUpdateAt' => ['applyDate', 'c.lastUpdateAt'],
			],
			[
				'userIdentifier' => 'c.userIdentifier',
				'createdAt' => 'c.createdAt',
				'lastUpdateAt' => 'c.lastUpdateAt',
			]
		);
	}
}
