<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\GlobalSettingsForm\Event;

use Throwable;
use Symfony\Contracts\EventDispatcher\Event;

final class GlobalSettingsUpdateFailedEvent extends Event
{
	private Throwable $error;

	/**
	 * @param \Throwable $error
	 */
	public function __construct(Throwable $error)
	{
		$this->error = $error;
	}

	/**
	 * @return \Throwable
	 */
	public function getError(): Throwable
	{
		return $this->error;
	}
}
