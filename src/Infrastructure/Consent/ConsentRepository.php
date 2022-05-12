<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent;

use App\Domain\Consent\Consent;
use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Consent\ConsentRepositoryInterface;
use App\Domain\Consent\Exception\ConsentNotFoundException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Common\Repository\AggregateRootRepositoryInterface;

final class ConsentRepository implements ConsentRepositoryInterface
{
	private AggregateRootRepositoryInterface $aggregateRootRepository;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Infrastructure\Common\Repository\AggregateRootRepositoryInterface $aggregateRootRepository
	 */
	public function __construct(AggregateRootRepositoryInterface $aggregateRootRepository)
	{
		$this->aggregateRootRepository = $aggregateRootRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	public function save(Consent $consent): void
	{
		$this->aggregateRootRepository->saveAggregateRoot($consent);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(ConsentId $id): Consent
	{
		$consent = $this->aggregateRootRepository->loadAggregateRoot(Consent::class, AggregateId::fromUuid($id->id()));

		if (!$consent instanceof Consent) {
			throw ConsentNotFoundException::withId($id);
		}

		return $consent;
	}
}
