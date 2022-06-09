<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\BasicInformation\Event;

use Symfony\Contracts\EventDispatcher\Event;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

final class BasicInformationUpdatedEvent extends Event
{
	private UserId $userId;

	private string $oldProfile;

	private string $newProfile;

	/**
	 * @param \SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId $userId
	 * @param string                                                     $oldProfile
	 * @param string                                                     $newProfile
	 */
	public function __construct(UserId $userId, string $oldProfile, string $newProfile)
	{
		$this->userId = $userId;
		$this->oldProfile = $oldProfile;
		$this->newProfile = $newProfile;
	}

	/**
	 * @return \SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId
	 */
	public function userId(): UserId
	{
		return $this->userId;
	}

	/**
	 * @return string
	 */
	public function oldProfile(): string
	{
		return $this->oldProfile;
	}

	/**
	 * @return string
	 */
	public function newProfile(): string
	{
		return $this->newProfile;
	}
}
