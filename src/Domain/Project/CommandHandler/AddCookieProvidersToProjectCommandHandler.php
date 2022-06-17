<?php

declare(strict_types=1);

namespace App\Domain\Project\CommandHandler;

use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\Project\Command\AddCookieProvidersToProjectCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class AddCookieProvidersToProjectCommandHandler implements CommandHandlerInterface
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
	 * @param \App\Domain\Project\Command\AddCookieProvidersToProjectCommand $command
	 *
	 * @return void
	 */
	public function __invoke(AddCookieProvidersToProjectCommand $command): void
	{
		$project = $this->projectRepository->get(ProjectId::fromString($command->projectId()));

		foreach ($command->cookieProviderIds() as $cookieProviderId) {
			$project->addCookieProvider(CookieProviderId::fromString($cookieProviderId));
		}

		$this->projectRepository->save($project);
	}
}
