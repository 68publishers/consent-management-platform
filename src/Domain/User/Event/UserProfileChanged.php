<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\Shared\ValueObject\Locale;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class UserProfileChanged extends AbstractDomainEvent
{
	private UserId $userId;

	private Locale $profileLocale;

	/***
	 * @param \SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId $userId
	 * @param \App\Domain\Shared\ValueObject\Locale $profileLocale
	 *
	 * @return static
	 */
	public static function create(UserId $userId, Locale $profileLocale): self
	{
		$event = self::occur($userId->toString(), [
			'profile_locale' => $profileLocale->value(),
		]);

		$event->userId = $userId;
		$event->profileLocale = $profileLocale;

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
	 * @return \App\Domain\Shared\ValueObject\Locale
	 */
	public function profileLocale(): Locale
	{
		return $this->profileLocale;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->userId = UserId::fromUuid($this->aggregateId()->id());
		$this->profileLocale = Locale::fromValue($parameters['profile_locale']);
	}
}
