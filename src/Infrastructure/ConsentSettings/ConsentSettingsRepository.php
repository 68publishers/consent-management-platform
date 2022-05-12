<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings;

use App\Domain\ConsentSettings\ConsentSettings;
use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use App\Domain\ConsentSettings\ConsentSettingsRepositoryInterface;
use App\Domain\ConsentSettings\Exception\ConsentSettingsNotFoundException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Common\Repository\AggregateRootRepositoryInterface;

final class ConsentSettingsRepository implements ConsentSettingsRepositoryInterface
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
	public function save(ConsentSettings $consentSettings): void
	{
		$this->aggregateRootRepository->saveAggregateRoot($consentSettings);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(ConsentSettingsId $id): ConsentSettings
	{
		$consentSettings = $this->aggregateRootRepository->loadAggregateRoot(ConsentSettings::class, AggregateId::fromUuid($id->id()));

		if (!$consentSettings instanceof ConsentSettings) {
			throw ConsentSettingsNotFoundException::withId($id);
		}

		return $consentSettings;
	}
}
