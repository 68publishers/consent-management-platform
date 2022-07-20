<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine\ReadModel;

use Doctrine\ORM\QueryBuilder;
use App\Domain\Project\Project;
use Doctrine\ORM\Query\Expr\Join;
use App\Domain\ConsentSettings\ConsentSettings;
use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\ReadModel\ConsentSettings\ConsentSettingsDataGridQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class ConsentSettingsDataGridQueryHandler implements QueryHandlerInterface
{
	use DataGridQueryHandlerTrait;

	/**
	 * @param \App\ReadModel\ConsentSettings\ConsentSettingsDataGridQuery $query
	 *
	 * @return array|int
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(ConsentSettingsDataGridQuery $query)
	{
		return $this->processQuery(
			$query,
			function (ConsentSettingsDataGridQuery $query): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('COUNT(c.id)')
					->from(ConsentSettings::class, 'c')
					->join(Project::class, 'p', Join::WITH, 'c.projectId = p.id AND p.id = :projectId AND p.deletedAt IS NULL')
					->setParameter('projectId', $query->projectId());
			},
			function (ConsentSettingsDataGridQuery $query): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('c')
					->from(ConsentSettings::class, 'c')
					->join(Project::class, 'p', Join::WITH, 'c.projectId = p.id AND p.id = :projectId AND p.deletedAt IS NULL')
					->setParameter('projectId', $query->projectId());
			},
			ConsentSettingsView::class,
			[
				'checksum' => ['applyLike', 'c.checksum'],
				'shortIdentifier' => ['applyEquals', 'c.shortIdentifier'],
				'createdAt' => ['applyDate', 'c.createdAt'],
				'lastUpdateAt' => ['applyDate', 'c.lastUpdateAt'],
			],
			[
				'checksum' => 'c.checksum',
				'shortIdentifier' => 'c.shortIdentifier',
				'createdAt' => 'c.createdAt',
				'lastUpdateAt' => 'c.lastUpdateAt',
			]
		);
	}
}
