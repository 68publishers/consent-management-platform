<?php

declare(strict_types=1);

namespace App\Infrastructure\Import\Doctrine\ReadModel;

use App\Domain\Import\Import;
use Doctrine\ORM\QueryBuilder;
use App\ReadModel\Import\ImportView;
use App\ReadModel\Import\ImportDataGridQuery;
use App\Infrastructure\DataGridQueryHandlerTrait;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class ImportDataGridQueryHandler implements QueryHandlerInterface
{
	use DataGridQueryHandlerTrait;

	/**
	 * @param \App\ReadModel\Import\ImportDataGridQuery $query
	 *
	 * @return array|int
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(ImportDataGridQuery $query)
	{
		return $this->processQuery(
			$query,
			function (): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('COUNT(i.id)')
					->from(Import::class, 'i');
			},
			function (): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('i')
					->from(Import::class, 'i');
			},
			ImportView::class,
			[
				'createdAt' => ['applyDate', 'i.createdAt'],
				'endedAt' => ['applyDate', 'i.endedAt'],
				'name' => ['applyIn', 'i.name'],
				'status' => ['applyIn', 'i.status'],
				'author' => ['applyLike', 'i.author'],
			],
			[
				'createdAt' => 'i.createdAt',
				'endedAt' => 'i.endedAt',
				'name' => 'i.name',
				'author' => 'i.author',
				'imported' => 'i.imported',
				'failed' => 'i.failed',
				'warned' => 'i.warned',
			]
		);
	}
}
