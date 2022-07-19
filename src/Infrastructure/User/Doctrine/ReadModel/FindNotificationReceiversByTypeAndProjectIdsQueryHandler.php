<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Doctrine\ReadModel;

use App\Domain\User\User;
use App\Domain\Project\Project;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\User\NotificationReceiverView;
use App\ReadModel\User\FindNotificationReceiversByTypeAndProjectIdsQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\BatchGeneratorFactory;

final class FindNotificationReceiversByTypeAndProjectIdsQueryHandler implements QueryHandlerInterface
{
	private EntityManagerInterface $em;

	private BatchGeneratorFactory $batchGeneratorFactory;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface                                                             $em
	 * @param \SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\BatchGeneratorFactory $batchGeneratorFactory
	 */
	public function __construct(EntityManagerInterface $em, BatchGeneratorFactory $batchGeneratorFactory)
	{
		$this->em = $em;
		$this->batchGeneratorFactory = $batchGeneratorFactory;
	}

	/**
	 * @param \App\ReadModel\User\FindNotificationReceiversByTypeAndProjectIdsQuery $query
	 *
	 * @return iterable|\SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\Batch[]
	 * @throws \JsonException
	 */
	public function __invoke(FindNotificationReceiversByTypeAndProjectIdsQuery $query): iterable
	{
		$qb = $this->em->createQueryBuilder()
			->select('u.emailAddress, u.name.firstname, u.name.surname, u.profileLocale, JSON_AGG(DISTINCT p.id) AS projectIds')
			->from(User::class, 'u')
			->join('u.projects', 'uhp', Join::WITH)
			->join(Project::class, 'p', Join::WITH, 'p.id = uhp.projectId AND p.deletedAt IS NULL')
			->where('u.deletedAt IS NULL')
			->andWhere('JSONB_CONTAINS(u.notificationPreferences, :notificationType) = true')
			->setParameters([
				'notificationType' => json_encode($query->notificationType(), JSON_THROW_ON_ERROR),
			])
			->groupBy('u.emailAddress', 'u.name.firstname', 'u.name.surname', 'u.profileLocale');

		if (!empty($query->projectIdsOnly())) {
			$qb->andWhere('p.id IN (:projectIds)')
				->setParameter('projectIds', $query->projectIdsOnly());
		}

		return $this->batchGeneratorFactory->create($query, $qb->getQuery(), NotificationReceiverView::class, FALSE, FALSE);
	}
}
