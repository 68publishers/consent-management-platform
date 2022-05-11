<?php

declare(strict_types=1);

namespace App\Domain\Project\CommandHandler;

use App\Domain\Project\Project;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Project\CheckCodeUniquenessInterface;
use App\Domain\Project\Command\CreateProjectCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class CreateProjectCommandHandler implements CommandHandlerInterface
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
	 * @param \App\Domain\Project\Command\CreateProjectCommand $command
	 *
	 * @return void
	 */
	public function __invoke(CreateProjectCommand $command): void
	{
		$project = Project::create($command, $this->checkCodeUniqueness);

		$this->projectRepository->save($project);
	}
}
