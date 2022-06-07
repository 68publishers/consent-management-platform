<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Event;

use App\Domain\CookieProvider\ValueObject\Code;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieProviderCodeChanged extends AbstractDomainEvent
{
	private CookieProviderId $cookieProviderId;

	private Code $code;

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param \App\Domain\CookieProvider\ValueObject\Code             $code
	 *
	 * @return static
	 */
	public static function create(CookieProviderId $cookieProviderId, Code $code): self
	{
		$event = self::occur($cookieProviderId->toString(), [
			'code' => $code->value(),
		]);

		$event->cookieProviderId = $cookieProviderId;
		$event->code = $code;

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
	 * @return \App\Domain\CookieProvider\ValueObject\Code
	 */
	public function code(): Code
	{
		return $this->code;
	}

	/**
	 * @param array $parameters
	 *
	 * @return void
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->cookieProviderId = CookieProviderId::fromUuid($this->aggregateId()->id());
		$this->code = Code::fromValue($parameters['code']);
	}
}
