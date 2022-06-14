<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieProcessingTimeChanged extends AbstractDomainEvent
{
	private CookieId $cookieId;

	private ProcessingTime $processingTime;

	/**
	 * @param \App\Domain\Cookie\ValueObject\CookieId       $cookieId
	 * @param \App\Domain\Cookie\ValueObject\ProcessingTime $processingTime
	 *
	 * @return static
	 */
	public static function create(CookieId $cookieId, ProcessingTime $processingTime): self
	{
		$event = self::occur($cookieId->toString(), [
			'processing_time' => $processingTime->value(),
		]);

		$event->cookieId = $cookieId;
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
		$this->processingTime = ProcessingTime::fromValue($parameters['processing_time']);
	}
}
