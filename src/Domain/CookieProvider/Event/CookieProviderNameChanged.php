<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Event;

use App\Domain\CookieProvider\ValueObject\Name;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieProviderNameChanged extends AbstractDomainEvent
{
	private CookieProviderId $cookieProviderId;

	private Name $name;

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param \App\Domain\CookieProvider\ValueObject\Name             $name
	 *
	 * @return static
	 */
	public static function create(CookieProviderId $cookieProviderId, Name $name): self
	{
		$event = self::occur($cookieProviderId->toString(), [
			'name' => $name->value(),
		]);

		$event->cookieProviderId = $cookieProviderId;
		$event->name = $name;

		return $event;
	}

	/**
	 * @return \App\Domain\CookieProvider\ValueObject\CookieProviderId
	 */
	public function cookieProviderId(): CookieProviderId
	{
		return $this->cookieProviderId;
	}

	/**
	 * @return \App\Domain\CookieProvider\ValueObject\Name
	 */
	public function name(): Name
	{
		return $this->name;
	}

	/**
	 * @param array $parameters
	 *
	 * @return void
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->cookieProviderId = CookieProviderId::fromUuid($this->aggregateId()->id());
		$this->name = Name::fromValue($parameters['name']);
	}
}
