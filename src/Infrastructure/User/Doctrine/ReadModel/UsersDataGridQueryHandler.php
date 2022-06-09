<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Doctrine\ReadModel;

use Doctrine\ORM\QueryBuilder;
use App\ReadModel\User\UserView;
use App\ReadModel\User\UsersDataGridQuery;
use App\Infrastructure\DataGridQueryHandlerTrait;
use SixtyEightPublishers\UserBundle\Domain\Aggregate\User;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class UsersDataGridQueryHandler implements QueryHandlerInterface
{
	use DataGridQueryHandlerTrait;

	/**
	 * @param \App\ReadModel\User\UsersDataGridQuery $query
	 *
	 * @return array|int
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(UsersDataGridQuery $query)
	{
		return $this->processQuery(
			$query,
			function (): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('COUNT(u.id)')
					->from(User::class, 'u')
					->where('u.deletedAt IS NULL');
			},
			function (): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('u')
					->from(User::class, 'u')
					->where('u.deletedAt IS NULL');
			},
			UserView::class,
			[
				'id' => ['applyLike', 'CAST(u.id AS TEXT)'],
				'emailAddress' => ['applyLike', 'u.emailAddress'],
				'name' => ['applyLike', 'CONCAT(u.name.firstname, \' \', u.name.surname)'],
				'createdAt' => ['applyDate', 'u.createdAt'],
				'roles' => ['applyJsonbContains', 'u.roles'],
			],
			[
				'emailAddress' => 'u.emailAddress',
				'name' => ['u.name.firstname', 'u.name.surname'],
				'createdAt' => 'u.createdAt',
			]
		);
	}
}
