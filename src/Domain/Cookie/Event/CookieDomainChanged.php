<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Cookie\ValueObject\Domain;
use App\Domain\Cookie\ValueObject\CookieId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieDomainChanged extends AbstractDomainEvent
{
	private CookieId $cookieId;

	private Domain $domain;

	public static function create(CookieId $cookieId, Domain $domain): self
	{
		$event = self::occur($cookieId->toString(), [
			'domain' => $domain->value(),
		]);

		$event->cookieId = $cookieId;
		$event->domain = $domain;

		return $event;
	}

	public function cookieId(): CookieId
	{
		return $this->cookieId;
	}

	public function domain(): Domain
	{
		return $this->domain;
	}

	protected function reconstituteState(array $parameters): void
	{
		$this->cookieId = CookieId::fromUuid($this->aggregateId()->id());
		$this->domain = Domain::fromValue($parameters['domain']);
	}
}
