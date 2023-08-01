<?php

declare(strict_types=1);

namespace App\Subscribers\Project;

use App\Domain\Project\Event\ProjectCreated;
use App\Domain\User\Command\AssignProjectsToUserCommand;
use Nette\Security\User as NetteUser;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;
use SixtyEightPublishers\UserBundle\Application\Authentication\Identity;

final class AssignCreatedProjectToLoggedUser implements EventHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly NetteUser $user,
    ) {}

    public function __invoke(ProjectCreated $event): void
    {
        if (!$this->user->isLoggedIn()) {
            return;
        }

        $identity = $this->user->getIdentity();

        if (!$identity instanceof Identity) {
            return;
        }

        $this->commandBus->dispatch(AssignProjectsToUserCommand::create(
            $identity->id()->toString(),
            [$event->projectId()->toString()],
        ));
    }
}
