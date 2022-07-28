<?php

declare(strict_types=1);

namespace App\Infrastructure\Import;

use App\Domain\Import\Import;
use App\Domain\Import\ValueObject\ImportId;
use App\Domain\Import\ImportRepositoryInterface;
use App\Domain\Import\Exception\ImportNotFoundException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Common\Repository\AggregateRootRepositoryInterface;

final class ImportRepository implements ImportRepositoryInterface
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
	public function save(Import $import): void
	{
		$this->aggregateRootRepository->saveAggregateRoot($import);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(ImportId $id): Import
	{
		$import = $this->aggregateRootRepository->loadAggregateRoot(Import::class, AggregateId::fromUuid($id->id()));

		if (!$import instanceof Import) {
			throw ImportNotFoundException::withId($id);
		}

		return $import;
	}
}
