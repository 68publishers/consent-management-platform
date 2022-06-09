<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\ProviderForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

final class ProviderCreatedEvent extends Event
{
	private CookieProviderId $cookieProviderId;

	private string $code;

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param string                                                  $code
	 */
	public function __construct(CookieProviderId $cookieProviderId, string $code)
	{
		$this->cookieProviderId = $cookieProviderId;
		$this->code = $code;
	}

	/**
	 * @return \App\Domain\CookieProvider\ValueObject\CookieProviderId
	 */
	public function cookieProviderId(): CookieProviderId
	{
		return $this->cookieProviderId;
	}

	/**
	 * @return string
	 */
	public function code(): string
	{
		return $this->code;
	}
}
