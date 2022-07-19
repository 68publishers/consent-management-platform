<?php

declare(strict_types=1);

namespace App\Domain\User\CommandHandler;

use App\Domain\User\User;
use App\Domain\User\ValueObject\NotificationPreferences;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use App\Domain\User\Command\ChangeNotificationPreferencesCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;
use SixtyEightPublishers\UserBundle\Domain\Repository\UserRepositoryInterface;

final class ChangeNotificationPreferencesCommandHandler implements CommandHandlerInterface
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
	 * @param \App\Domain\User\Command\ChangeNotificationPreferencesCommand $command
	 *
	 * @return void
	 */
	public function __invoke(ChangeNotificationPreferencesCommand $command): void
	{
		$user = $this->userRepository->get(UserId::fromString($command->userId()));
		assert($user instanceof User);

		$user->changeNotificationPreferences(NotificationPreferences::reconstitute($command->notificationTypes()));

		$this->userRepository->save($user);
	}
}
