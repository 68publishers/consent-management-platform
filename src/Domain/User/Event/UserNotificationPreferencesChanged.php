<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\User\ValueObject\NotificationPreferences;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class UserNotificationPreferencesChanged extends AbstractDomainEvent
{
	private UserId $userId;

	private NotificationPreferences $notificationPreferences;

	/**
	 * @param \SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId $userId
	 * @param \App\Domain\User\ValueObject\NotificationPreferences       $notificationPreferences
	 *
	 * @return static
	 */
	public static function create(UserId $userId, NotificationPreferences $notificationPreferences): self
	{
		$event = self::occur($userId->toString(), [
			'notification_preferences' => $notificationPreferences,
		]);

		$event->userId = $userId;
		$event->notificationPreferences = $notificationPreferences;

		return $event;
	}

	/**
	 * @return \SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId
	 */
	public function userId(): UserId
	{
		return $this->userId;
	}

	/**
	 * @return \App\Domain\User\ValueObject\NotificationPreferences
	 */
	public function notificationPreferences(): NotificationPreferences
	{
		return $this->notificationPreferences;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->userId = UserId::fromUuid($this->aggregateId()->id());
		$this->notificationPreferences = NotificationPreferences::reconstitute($parameters['notification_preferences']);
	}
}
