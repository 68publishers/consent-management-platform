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

	private ProcessingTime $processingTime;

	private Name $name;

	private bool $active;

	private array $purposes;

	/**
	 * @param \App\Domain\Cookie\ValueObject\CookieId                 $cookieId
	 * @param \App\Domain\Category\ValueObject\CategoryId             $categoryId
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param \App\Domain\Cookie\ValueObject\Name                     $name
	 * @param \App\Domain\Cookie\ValueObject\ProcessingTime           $processingTime
	 * @param bool                                                    $active
	 * @param \App\Domain\Cookie\ValueObject\Purpose[]                $purposes
	 *
	 * @return static
	 */
	public static function create(CookieId $cookieId, CategoryId $categoryId, CookieProviderId $cookieProviderId, Name $name, ProcessingTime $processingTime, bool $active, array $purposes): self
	{
		$event = self::occur($cookieId->toString(), [
			'category_id' => $categoryId->toString(),
			'cookie_provider_id' => $cookieProviderId->toString(),
			'name' => $name->value(),
			'processing_time' => $processingTime->value(),
			'active' => $active,
			'purposes' => array_map(static fn (Purpose $purpose): string => $purpose->value(), $purposes),
		]);

		$event->cookieId = $cookieId;
		$event->categoryId = $categoryId;
		$event->cookieProviderId = $cookieProviderId;
		$event->name = $name;
		$event->processingTime = $processingTime;
		$event->active = $active;
		$event->purposes = $purposes;

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
	 * @return \App\Domain\Cookie\ValueObject\ProcessingTime
	 */
	public function processingTime(): ProcessingTime
	{
		return $this->processingTime;
	}

	/**
	 * @return bool
	 */
	public function active(): bool
	{
		return $this->active;
	}

	/**
	 * @return \App\Domain\Cookie\ValueObject\Purpose[]
	 */
	public function purposes(): array
	{
		return $this->purposes;
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
		$this->processingTime = ProcessingTime::fromValue($parameters['processing_time']);
		$this->active = (bool) $parameters['active'];
		$this->purposes = array_map(static fn (string $purpose): Purpose => Purpose::fromValue($purpose), $parameters['purposes']);
	}
}
