<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Event;

use App\Domain\CookieProvider\ValueObject\Link;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieProviderLinkChanged extends AbstractDomainEvent
{
	private CookieProviderId $cookieProviderId;

	private Link $link;

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param \App\Domain\CookieProvider\ValueObject\Link             $link
	 *
	 * @return static
	 */
	public static function create(CookieProviderId $cookieProviderId, Link $link): self
	{
		$event = self::occur($cookieProviderId->toString(), [
			'link' => $link->value(),
		]);

		$event->cookieProviderId = $cookieProviderId;
		$event->link = $link;

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
	 * @return \App\Domain\CookieProvider\ValueObject\Link
	 */
	public function link(): Link
	{
		return $this->link;
	}

	/**
	 * @param array $parameters
	 *
	 * @return void
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->cookieProviderId = CookieProviderId::fromUuid($this->aggregateId()->id());
		$this->link = Link::fromValue($parameters['link']);
	}
}
