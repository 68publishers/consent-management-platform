<?php

declare(strict_types=1);

namespace App\Domain\Project\CommandHandler;

use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Project\Command\DeleteProjectCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class DeleteProjectCommandHandler implements CommandHandlerInterface
{
	private ProjectRepositoryInterface $projectRepository;

	/**
	 * @param \App\Domain\Project\ProjectRepositoryInterface $projectRepository
	 */
	public function __construct(ProjectRepositoryInterface $projectRepository)
	{
		$this->projectRepository = $projectRepository;
	}

	/**
	 * @param \App\Domain\Project\Command\DeleteProjectCommand $command
	 *
	 * @return void
	 */
	public function __invoke(DeleteProjectCommand $command): void
	{
		$project = $this->projectRepository->get(ProjectId::fromString($command->projectId()));

		$project->delete();

		$this->projectRepository->save($project);
	}
}
