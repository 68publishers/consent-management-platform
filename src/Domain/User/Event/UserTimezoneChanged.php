<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use DateTimeZone;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class UserTimezoneChanged extends AbstractDomainEvent
{
	private UserId $userId;

	private DateTimeZone $timezone;

	/***
	 * @param \SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId $userId
	 * @param \DateTimeZone $timezone
	 *
	 * @return static
	 */
	public static function create(UserId $userId, DateTimeZone $timezone): self
	{
		$event = self::occur($userId->toString(), [
			'timezone' => $timezone->getName(),
		]);

		$event->userId = $userId;
		$event->timezone = $timezone;

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
	 * @return \DateTimeZone
	 */
	public function timezone(): DateTimeZone
	{
		return $this->timezone;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->userId = UserId::fromUuid($this->aggregateId()->id());
		$this->timezone = new DateTimeZone($parameters['timezone']);
	}
}
