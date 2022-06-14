<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Cookie\ValueObject\Purpose;
use App\Domain\Cookie\ValueObject\CookieId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookiePurposeChanged extends AbstractDomainEvent
{
	private CookieId $cookieId;

	private Locale $locale;

	private Purpose $purpose;

	/**
	 * @param \App\Domain\Cookie\ValueObject\CookieId $cookieId
	 * @param \App\Domain\Shared\ValueObject\Locale   $locale
	 * @param \App\Domain\Cookie\ValueObject\Purpose  $purpose
	 *
	 * @return static
	 */
	public static function create(CookieId $cookieId, Locale $locale, Purpose $purpose): self
	{
		$event = self::occur($cookieId->toString(), [
			'locale' => $locale->value(),
			'purpose' => $purpose->value(),
		]);

		$event->cookieId = $cookieId;
		$event->locale = $locale;
		$event->purpose = $purpose;

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
	 * @return \App\Domain\Shared\ValueObject\Locale
	 */
	public function locale(): Locale
	{
		return $this->locale;
	}

	/**
	 * @return \App\Domain\Cookie\ValueObject\Purpose
	 */
	public function purpose(): Purpose
	{
		return $this->purpose;
	}

	/**
	 * @param array $parameters
	 *
	 * @return void
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->cookieId = CookieId::fromUuid($this->aggregateId()->id());
		$this->locale = Locale::fromValue($parameters['locale']);
		$this->purpose = Purpose::fromValue($parameters['purpose']);
	}
}
