<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProviderForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

final class ProviderUpdatedEvent extends Event
{
	private CookieProviderId $cookieProviderId;

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 */
	public function __construct(CookieProviderId $cookieProviderId)
	{
		$this->cookieProviderId = $cookieProviderId;
	}

	/**
	 * @return \App\Domain\CookieProvider\ValueObject\CookieProviderId
	 */
	public function cookieProviderId(): CookieProviderId
	{
		return $this->cookieProviderId;
	}
}
