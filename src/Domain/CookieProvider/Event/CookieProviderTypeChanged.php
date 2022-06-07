<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Event;

use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieProviderTypeChanged extends AbstractDomainEvent
{
	private CookieProviderId $cookieProviderId;

	private ProviderType $type;

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param \App\Domain\CookieProvider\ValueObject\ProviderType     $type
	 *
	 * @return static
	 */
	public static function create(CookieProviderId $cookieProviderId, ProviderType $type): self
	{
		$event = self::occur($cookieProviderId->toString(), [
			'type' => $type->value(),
		]);

		$event->cookieProviderId = $cookieProviderId;
		$event->type = $type;

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
	 * @return \App\Domain\CookieProvider\ValueObject\ProviderType
	 */
	public function type(): ProviderType
	{
		return $this->type;
	}

	/**
	 * @param array $parameters
	 *
	 * @return void
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->cookieProviderId = CookieProviderId::fromUuid($this->aggregateId()->id());
		$this->type = ProviderType::fromValue($parameters['type']);
	}
}
