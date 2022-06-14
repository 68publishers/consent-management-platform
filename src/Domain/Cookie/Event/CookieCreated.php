<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Cookie\ValueObject\Name;
use App\Domain\Cookie\ValueObject\Purpose;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieCreated extends AbstractDomainEvent
{
	private CookieId $cookieId;

	private CategoryId $categoryId;

	private CookieProviderId $cookieProviderId;

	private Name $name;

	private array $purposes;

	private array $processingTimes;

	/**
	 * @param \App\Domain\Cookie\ValueObject\CookieId                 $cookieId
	 * @param \App\Domain\Category\ValueObject\CategoryId             $categoryId
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param \App\Domain\Cookie\ValueObject\Name                     $name
	 * @param \App\Domain\Cookie\ValueObject\Purpose[]                $purposes
	 * @param \App\Domain\Cookie\ValueObject\ProcessingTime[]         $processingTimes
	 *
	 * @return static
	 */
	public static function create(CookieId $cookieId, CategoryId $categoryId, CookieProviderId $cookieProviderId, Name $name, array $purposes, array $processingTimes): self
	{
		$event = self::occur($cookieId->toString(), [
			'category_id' => $categoryId->toString(),
			'cookie_provider_id' => $cookieProviderId->toString(),
			'name' => $name->value(),
			'purposes' => array_map(static fn (Purpose $purpose): string => $purpose->value(), $purposes),
			'processing_times' => array_map(static fn (ProcessingTime $processingTime): string => $processingTime->value(), $processingTimes),
		]);

		$event->cookieId = $cookieId;
		$event->categoryId = $categoryId;
		$event->cookieProviderId = $cookieProviderId;
		$event->name = $name;
		$event->purposes = $purposes;
		$event->processingTimes = $processingTimes;

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
	 * @return \App\Domain\Category\ValueObject\CategoryId
	 */
	public function categoryId(): CategoryId
	{
		return $this->categoryId;
	}

	/**
	 * @return \App\Domain\CookieProvider\ValueObject\CookieProviderId
	 */
	public function cookieProviderId(): CookieProviderId
	{
		return $this->cookieProviderId;
	}

	/**
	 * @return \App\Domain\Cookie\ValueObject\Name
	 */
	public function name(): Name
	{
		return $this->name;
	}

	/**
	 * @return \App\Domain\Cookie\ValueObject\Purpose[]
	 */
	public function purposes(): array
	{
		return $this->purposes;
	}

	/**
	 * @return \App\Domain\Cookie\ValueObject\ProcessingTime[]
	 */
	public function processingTimes(): array
	{
		return $this->processingTimes;
	}

	/**
	 * @param array $parameters
	 *
	 * @return void
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->cookieId = CookieId::fromUuid($this->aggregateId()->id());
		$this->categoryId = CategoryId::fromString($parameters['category_id']);
		$this->cookieProviderId = CookieProviderId::fromString($parameters['cookie_provider_id']);
		$this->name = Name::fromValue($parameters['name']);
		$this->purposes = array_map(static fn (string $purpose): Purpose => Purpose::fromValue($purpose), $parameters['purposes']);
		$this->processingTimes = array_map(static fn (string $processingTime): ProcessingTime => ProcessingTime::fromValue($processingTime), $parameters['processing_times']);
	}
}
