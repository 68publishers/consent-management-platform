<?php

declare(strict_types=1);

namespace App\Infrastructure\Project;

use App\Domain\Project\Project;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Project\Exception\ProjectNotFoundException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Common\Repository\AggregateRootRepositoryInterface;

final class ProjectRepository implements ProjectRepositoryInterface
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
	public function save(Project $project): void
	{
		$this->aggregateRootRepository->saveAggregateRoot($project);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(ProjectId $id): Project
	{
		$project = $this->aggregateRootRepository->loadAggregateRoot(Project::class, AggregateId::fromUuid($id->id()));

		if (!$project instanceof Project) {
			throw ProjectNotFoundException::withId($id);
		}

		return $project;
	}
}
