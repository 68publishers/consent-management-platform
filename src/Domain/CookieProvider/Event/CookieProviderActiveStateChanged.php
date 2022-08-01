<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Event;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieProviderActiveStateChanged extends AbstractDomainEvent
{
	private CookieProviderId $cookieProviderId;

	private bool $active;

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param bool                                                    $active
	 *
	 * @return static
	 */
	public static function create(CookieProviderId $cookieProviderId, bool $active): self
	{
		$event = self::occur($cookieProviderId->toString(), [
			'active' => $active,
		]);

		$event->cookieProviderId = $cookieProviderId;
		$event->active = $active;

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
	 * @return bool
	 */
	public function active(): bool
	{
		return $this->active;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->cookieProviderId = CookieProviderId::fromUuid($this->aggregateId()->id());
		$this->active = (bool) $parameters['active'];
	}
}
