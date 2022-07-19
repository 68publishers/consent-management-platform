<?php

declare(strict_types=1);

namespace App\Domain\User\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class ChangeNotificationPreferencesCommand extends AbstractCommand
{
	/**
	 * @param string $userId
	 * @param string ...$notificationTypes
	 *
	 * @return static
	 */
	public static function create(string $userId, string ...$notificationTypes): self
	{
		return self::fromParameters([
			'user_id' => $userId,
			'notification_types' => $notificationTypes,
		]);
	}

	/**
	 * @return string
	 */
	public function userId(): string
	{
		return $this->getParam('user_id');
	}

	/**
	 * @return array
	 */
	public function notificationTypes(): array
	{
		return $this->getParam('notification_types');
	}
}
