<?php

declare(strict_types=1);

namespace App\Domain\Project\CommandHandler;

use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Project\CheckCodeUniquenessInterface;
use App\Domain\Project\Command\UpdateProjectCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class UpdateProjectCommandHandler implements CommandHandlerInterface
{
	private ProjectRepositoryInterface $projectRepository;

	private CheckCodeUniquenessInterface $checkCodeUniqueness;

	/**
	 * @param \App\Domain\Project\ProjectRepositoryInterface   $projectRepository
	 * @param \App\Domain\Project\CheckCodeUniquenessInterface $checkCodeUniqueness
	 */
	public function __construct(ProjectRepositoryInterface $projectRepository, CheckCodeUniquenessInterface $checkCodeUniqueness)
	{
		$this->projectRepository = $projectRepository;
		$this->checkCodeUniqueness = $checkCodeUniqueness;
	}

	/**
	 * @param \App\Domain\Project\Command\UpdateProjectCommand $command
	 *
	 * @return void
	 */
	public function __invoke(UpdateProjectCommand $command): void
	{
		$project = $this->projectRepository->get(ProjectId::fromString($command->projectId()));

		$project->update($command, $this->checkCodeUniqueness);

		$this->projectRepository->save($project);
	}
}
