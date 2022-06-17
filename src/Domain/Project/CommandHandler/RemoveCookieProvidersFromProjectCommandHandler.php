<?php

declare(strict_types=1);

namespace App\Domain\Project\CommandHandler;

use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\Project\Command\RemoveCookieProvidersFromProjectCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class RemoveCookieProvidersFromProjectCommandHandler implements CommandHandlerInterface
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
	 * @param \App\Domain\Project\Command\RemoveCookieProvidersFromProjectCommand $command
	 *
	 * @return void
	 */
	public function __invoke(RemoveCookieProvidersFromProjectCommand $command): void
	{
		$project = $this->projectRepository->get(ProjectId::fromString($command->projectId()));

		foreach ($command->cookieProviderIds() as $cookieProviderId) {
			$project->removeCookieProvider(CookieProviderId::fromString($cookieProviderId));
		}

		$this->projectRepository->save($project);
	}
}
