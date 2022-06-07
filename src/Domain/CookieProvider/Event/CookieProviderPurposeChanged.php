<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Event;

use App\Domain\Shared\ValueObject\Locale;
use App\Domain\CookieProvider\ValueObject\Purpose;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieProviderPurposeChanged extends AbstractDomainEvent
{
	private CookieProviderId $cookieProviderId;

	private Locale $locale;

	private Purpose $purpose;

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param \App\Domain\Shared\ValueObject\Locale                   $locale
	 * @param \App\Domain\CookieProvider\ValueObject\Purpose          $purpose
	 *
	 * @return static
	 */
	public static function create(CookieProviderId $cookieProviderId, Locale $locale, Purpose $purpose): self
	{
		$event = self::occur($cookieProviderId->toString(), [
			'locale' => $locale->value(),
			'purpose' => $purpose->value(),
		]);

		$event->cookieProviderId = $cookieProviderId;
		$event->locale = $locale;
		$event->purpose = $purpose;

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
	 * @return \App\Domain\Shared\ValueObject\Locale
	 */
	public function locale(): Locale
	{
		return $this->locale;
	}

	/**
	 * @return \App\Domain\CookieProvider\ValueObject\Purpose
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
		$this->cookieProviderId = CookieProviderId::fromUuid($this->aggregateId()->id());
		$this->locale = Locale::fromValue($parameters['locale']);
		$this->purpose = Purpose::fromValue($parameters['purpose']);
	}
}
