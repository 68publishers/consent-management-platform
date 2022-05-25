<?php

declare(strict_types=1);

namespace App\Infrastructure\GlobalSettings\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use App\Domain\GlobalSettings\GlobalSettings;
use App\Domain\GlobalSettings\GlobalSettingsRepositoryInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Common\Repository\AggregateRootRepositoryInterface;

final class GlobalSettingsRepository implements GlobalSettingsRepositoryInterface
{
	private AggregateRootRepositoryInterface $aggregateRootRepository;

	private EntityManagerInterface $em;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Infrastructure\Common\Repository\AggregateRootRepositoryInterface $aggregateRootRepository
	 * @param \Doctrine\ORM\EntityManagerInterface                                                                       $em
	 */
	public function __construct(AggregateRootRepositoryInterface $aggregateRootRepository, EntityManagerInterface $em)
	{
		$this->aggregateRootRepository = $aggregateRootRepository;
		$this->em = $em;
	}

	/**
	 * {@inheritDoc}
	 */
	public function save(GlobalSettings $globalSettings): void
	{
		$this->aggregateRootRepository->saveAggregateRoot($globalSettings);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function get(): ?GlobalSettings
	{
		return $this->em->createQueryBuilder()
			->select('gs')
			->from(GlobalSettings::class, 'gs')
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();
	}
}
