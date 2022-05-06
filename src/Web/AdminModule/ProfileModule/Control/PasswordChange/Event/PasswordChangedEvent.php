<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\PasswordChange\Event;

use Symfony\Contracts\EventDispatcher\Event;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

final class PasswordChangedEvent extends Event
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
