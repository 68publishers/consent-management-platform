<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\QueryHandler;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Query\UsersDataGridQuery;
use SixtyEightPublishers\UserBundle\Domain\Aggregate\User;
use SixtyEightPublishers\UserBundle\ReadModel\View\UserView;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\UserBundle\Infrastructure\Doctrine\QueryHandler\ViewFactory;

final class UsersDataGridQueryHandler implements QueryHandlerInterface
{
	use DataGridQueryHandlerTrait;

	private EntityManagerInterface $em;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @param \App\ReadModel\Query\UsersDataGridQuery $query
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
					->from(User::class, 'u');
			},
			function (): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('u')
					->from(User::class, 'u');
			},
			static fn (array $data): UserView => ViewFactory::createUserView($data),
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
