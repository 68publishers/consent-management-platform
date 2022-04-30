<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\ResetPassword\Event;

use Throwable;
use Symfony\Contracts\EventDispatcher\Event;

final class PasswordResetFailedEvent extends Event
{
	private Throwable $exception;

	/**
	 * @param \Throwable $exception
	 */
	public function __construct(Throwable $exception)
	{
		$this->exception = $exception;
	}

	/**
	 * @return \Throwable
	 */
	public function exception(): Throwable
	{
		return $this->exception;
	}
}
