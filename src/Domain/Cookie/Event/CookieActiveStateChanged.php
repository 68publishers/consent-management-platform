<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Cookie\ValueObject\CookieId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieActiveStateChanged extends AbstractDomainEvent
{
	private CookieId $cookieId;

	private bool $active;

	/**
	 * @param \App\Domain\Cookie\ValueObject\CookieId $cookieId
	 * @param bool                                    $active
	 *
	 * @return static
	 */
	public static function create(CookieId $cookieId, bool $active): self
	{
		$event = self::occur($cookieId->toString(), [
			'active' => $active,
		]);

		$event->cookieId = $cookieId;
		$event->active = $active;

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
		$this->cookieId = CookieId::fromUuid($this->aggregateId()->id());
		$this->active = (bool) $parameters['active'];
	}
}
