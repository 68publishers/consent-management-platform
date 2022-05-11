<?php

declare(strict_types=1);

namespace App\Infrastructure\PasswordRequest\Doctrine\QueryHandler;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\PasswordRequest\PasswordRequestsDataGridQuery;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Aggregate\PasswordRequest;
use SixtyEightPublishers\ForgotPasswordBundle\ReadModel\View\PasswordRequestView;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ForgotPasswordBundle\Infrastructure\Doctrine\QueryHandler\ViewFactory;

final class PasswordRequestsDataGridQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\PasswordRequest\PasswordRequestsDataGridQuery $query
	 *
	 * @return array|int
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(PasswordRequestsDataGridQuery $query)
	{
		return $this->processQuery(
			$query,
			function (): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('COUNT(pr.id)')
					->from(PasswordRequest::class, 'pr');
			},
			function (): QueryBuilder {
				return $this->em->createQueryBuilder()
					->select('pr')
					->from(PasswordRequest::class, 'pr');
			},
			static fn (array $data): PasswordRequestView => ViewFactory::createPasswordRequestView($data),
			[
				'id' => ['applyLike', 'CAST(pr.id AS TEXT)'],
				'emailAddress' => ['applyLike', 'pr.emailAddress'],
				'status' => ['applyIn', 'pr.status'],
				'requestedAt' => ['applyDate', 'pr.requestedAt'],
				'finishedAt' => ['applyDate', 'pr.finishedAt'],
			],
			[
				'emailAddress' => 'pr.emailAddress',
				'requestedAt' => 'pr.requestedAt',
				'finishedAt' => 'pr.finishedAt',
			]
		);
	}
}
