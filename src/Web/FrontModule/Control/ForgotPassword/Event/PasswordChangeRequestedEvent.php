<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\ForgotPassword\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class PasswordChangeRequestedEvent extends Event
{
	private string $emailAddress;

	/**
	 * @param string $emailAddress
	 */
	public function __construct(string $emailAddress)
	{
		$this->emailAddress = $emailAddress;
	}

	/**
	 * @return string
	 */
	public function emailAddress(): string
	{
		return $this->emailAddress;
	}
}
