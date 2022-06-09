<?php

declare(strict_types=1);

namespace App\Infrastructure\PasswordRequest\Doctrine\ReadModel;

use Doctrine\ORM\QueryBuilder;
use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\PasswordRequest\PasswordRequestsDataGridQuery;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Aggregate\PasswordRequest;
use SixtyEightPublishers\ForgotPasswordBundle\ReadModel\View\PasswordRequestView;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class PasswordRequestsDataGridQueryHandler implements QueryHandlerInterface
{
	use DataGridQueryHandlerTrait;

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
			PasswordRequestView::class,
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
