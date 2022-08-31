<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProviderForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

final class ProviderUpdatedEvent extends Event
{
	private CookieProviderId $cookieProviderId;

	private string $oldCode;

	private string $newCode;

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param string                                                  $oldCode
	 * @param string                                                  $newCode
	 */
	public function __construct(CookieProviderId $cookieProviderId, string $oldCode, string $newCode)
	{
		$this->cookieProviderId = $cookieProviderId;
		$this->oldCode = $oldCode;
		$this->newCode = $newCode;
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
	public function oldCode(): string
	{
		return $this->oldCode;
	}

	/**
	 * @return string
	 */
	public function newCode(): string
	{
		return $this->newCode;
	}
}
