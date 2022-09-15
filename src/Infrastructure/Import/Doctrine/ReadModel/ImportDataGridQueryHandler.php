<?php

declare(strict_types=1);

namespace App\Infrastructure\Import\Doctrine\ReadModel;

use App\Domain\User\User;
use App\Domain\Import\Import;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use App\ReadModel\Import\ImportListView;
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
					->from(Import::class, 'i')
					->leftJoin(User::class, 'u', Join::WITH, 'u.id = i.authorId AND u.deletedAt IS NULL');
			},
			function (): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('i.id, i.createdAt, i.endedAt, i.name, i.status, i.imported, i.failed, i.warned')
					->addSelect('u.id AS authorId, u.name.firstname, u.name.surname')
					->from(Import::class, 'i')
					->leftJoin(User::class, 'u', Join::WITH, 'u.id = i.authorId AND u.deletedAt IS NULL');
			},
			ImportListView::class,
			[
				'createdAt' => ['applyDate', 'i.createdAt'],
				'endedAt' => ['applyDate', 'i.endedAt'],
				'name' => ['applyIn', 'i.name'],
				'status' => ['applyIn', 'i.status'],
				'authorName' => ['applyLike', 'CONCAT(u.name.firstname, \'\', u.name.surname)'],
			],
			[
				'createdAt' => 'i.createdAt',
				'endedAt' => 'i.endedAt',
				'name' => 'i.name',
				'authorName' => 'CONCAT(u.name.firstname, \'\', u.name.surname)',
				'imported' => 'i.imported',
				'failed' => 'i.failed',
				'warned' => 'i.warned',
			]
		);
	}
}
