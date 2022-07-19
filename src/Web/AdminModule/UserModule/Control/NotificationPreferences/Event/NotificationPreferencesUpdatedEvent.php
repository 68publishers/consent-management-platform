<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\NotificationPreferences\Event;

use Symfony\Contracts\EventDispatcher\Event;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

final class NotificationPreferencesUpdatedEvent extends Event
{
	private UserId $userId;

	public function __construct(UserId $userId)
	{
		$this->userId = $userId;
	}

	/**
	 * @return \SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId
	 */
	public function userId(): UserId
	{
		return $this->userId;
	}
}
