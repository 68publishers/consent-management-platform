<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Cookie\ValueObject\Name;
use App\Domain\Cookie\ValueObject\CookieId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieNameChanged extends AbstractDomainEvent
{
	private CookieId $cookieId;

	private Name $name;

	/**
	 * @param \App\Domain\Cookie\ValueObject\CookieId $cookieId
	 * @param \App\Domain\Cookie\ValueObject\Name     $name
	 *
	 * @return static
	 */
	public static function create(CookieId $cookieId, Name $name): self
	{
		$event = self::occur($cookieId->toString(), [
			'name' => $name->value(),
		]);

		$event->cookieId = $cookieId;
		$event->name = $name;

		return $event;
	}

	/**
	 * @return \App\Domain\Cookie\ValueObject\CookieId
	 */
	public function cookieId(): CookieId
	{
		return $this->cookieId;
	}

	/**
	 * @return \App\Domain\Cookie\ValueObject\Name
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
		$this->cookieId = CookieId::fromUuid($this->aggregateId()->id());
		$this->name = Name::fromValue($parameters['name']);
	}
}
