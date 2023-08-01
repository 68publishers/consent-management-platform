<?php

declare(strict_types=1);

namespace App\Domain\Project\CommandHandler;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\Project\Command\RemoveCookieProvidersFromProjectCommand;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class RemoveCookieProvidersFromProjectCommandHandler implements CommandHandlerInterface
{
    private ProjectRepositoryInterface $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(RemoveCookieProvidersFromProjectCommand $command): void
    {
        $project = $this->projectRepository->get(ProjectId::fromString($command->projectId()));

        foreach ($command->cookieProviderIds() as $cookieProviderId) {
            $project->removeCookieProvider(CookieProviderId::fromString($cookieProviderId));
        }

        $this->projectRepository->save($project);
    }
}
