<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieProcessingTimeChanged extends AbstractDomainEvent
{
	private CookieId $cookieId;

	private Locale $locale;

	private ProcessingTime $processingTime;

	/**
	 * @param \App\Domain\Cookie\ValueObject\CookieId       $cookieId
	 * @param \App\Domain\Shared\ValueObject\Locale         $locale
	 * @param \App\Domain\Cookie\ValueObject\ProcessingTime $processingTime
	 *
	 * @return static
	 */
	public static function create(CookieId $cookieId, Locale $locale, ProcessingTime $processingTime): self
	{
		$event = self::occur($cookieId->toString(), [
			'locale' => $locale->value(),
			'processing_time' => $processingTime->value(),
		]);

		$event->cookieId = $cookieId;
		$event->locale = $locale;
		$event->processingTime = $processingTime;

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
	 * @return \App\Domain\Cookie\ValueObject\ProcessingTime
	 */
	public function processingTime(): ProcessingTime
	{
		return $this->processingTime;
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
		$this->processingTime = ProcessingTime::fromValue($parameters['processing_time']);
	}
}
