<?php

declare(strict_types=1);

namespace App\Domain\User\CommandHandler;

use App\Domain\User\User;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\User\Command\AssignProjectsToUserCommand;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;
use SixtyEightPublishers\UserBundle\Domain\Repository\UserRepositoryInterface;

final class AssignProjectsToUserCommandHandler implements CommandHandlerInterface
{
	private UserRepositoryInterface $userRepository;

	/**
	 * @param \SixtyEightPublishers\UserBundle\Domain\Repository\UserRepositoryInterface $userRepository
	 */
	public function __construct(UserRepositoryInterface $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * @param \App\Domain\User\Command\AssignProjectsToUserCommand $command
	 *
	 * @return void
	 */
	public function __invoke(AssignProjectsToUserCommand $command): void
	{
		$user = $this->userRepository->get(UserId::fromString($command->userId()));
		assert($user instanceof User);

		$user->addProjects(array_map(static fn (string $projectId): ProjectId => ProjectId::fromString($projectId), $command->projectIds()));

		$this->userRepository->save($user);
	}
}
