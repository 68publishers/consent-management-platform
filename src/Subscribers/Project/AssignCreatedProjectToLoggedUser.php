<?php

declare(strict_types=1);

namespace App\Subscribers\Project;

use Nette\Security\User as NetteUser;
use App\Domain\Project\Event\ProjectCreated;
use App\Domain\User\Command\AssignProjectsToUserCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;
use SixtyEightPublishers\UserBundle\Application\Authentication\Identity;

final class AssignCreatedProjectToLoggedUser implements EventHandlerInterface
{
	private CommandBusInterface $commandBus;

	private NetteUser $user;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \Nette\Security\User                                             $user
	 */
	public function __construct(CommandBusInterface $commandBus, NetteUser $user)
	{
		$this->commandBus = $commandBus;
		$this->user = $user;
	}

	/**
	 * @param \App\Domain\Project\Event\ProjectCreated $event
	 *
	 * @return void
	 */
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
			[$event->projectId()->toString()]
		));
	}
}
