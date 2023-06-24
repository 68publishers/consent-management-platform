<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Cookie\ValueObject\Name;
use App\Domain\Cookie\ValueObject\Domain;
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

	private Domain $domain;

	private bool $active;

	/** @var array<Purpose> */
	private array $purposes;

	/**
	 * @param array<Purpose> $purposes
	 */
	public static function create(CookieId $cookieId, CategoryId $categoryId, CookieProviderId $cookieProviderId, Name $name, Domain $domain, ProcessingTime $processingTime, bool $active, array $purposes): self
	{
		$event = self::occur($cookieId->toString(), [
			'category_id' => $categoryId->toString(),
			'cookie_provider_id' => $cookieProviderId->toString(),
			'name' => $name->value(),
			'domain' => $domain->value(),
			'processing_time' => $processingTime->value(),
			'active' => $active,
			'purposes' => array_map(static fn (Purpose $purpose): string => $purpose->value(), $purposes),
		]);

		$event->cookieId = $cookieId;
		$event->categoryId = $categoryId;
		$event->cookieProviderId = $cookieProviderId;
		$event->name = $name;
		$event->domain = $domain;
		$event->processingTime = $processingTime;
		$event->active = $active;
		$event->purposes = $purposes;

		return $event;
	}

	public function cookieId(): CookieId
	{
		return $this->cookieId;
	}

	public function categoryId(): CategoryId
	{
		return $this->categoryId;
	}

	public function cookieProviderId(): CookieProviderId
	{
		return $this->cookieProviderId;
	}

	public function name(): Name
	{
		return $this->name;
	}

	public function domain(): Domain
	{
		return $this->domain;
	}

	public function processingTime(): ProcessingTime
	{
		return $this->processingTime;
	}

	public function active(): bool
	{
		return $this->active;
	}

	public function purposes(): array
	{
		return $this->purposes;
	}

	protected function reconstituteState(array $parameters): void
	{
		$this->cookieId = CookieId::fromUuid($this->aggregateId()->id());
		$this->categoryId = CategoryId::fromString($parameters['category_id']);
		$this->cookieProviderId = CookieProviderId::fromString($parameters['cookie_provider_id']);
		$this->name = Name::fromValue($parameters['name']);
		$this->domain = Domain::fromValue($parameters['domain'] ?? '');
		$this->processingTime = ProcessingTime::fromValue($parameters['processing_time']);
		$this->active = (bool) $parameters['active'];
		$this->purposes = array_map(static fn (string $purpose): Purpose => Purpose::fromValue($purpose), $parameters['purposes']);
	}
}
