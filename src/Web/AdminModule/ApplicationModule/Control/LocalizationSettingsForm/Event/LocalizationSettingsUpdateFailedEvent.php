<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\LocalizationSettingsForm\Event;

use Throwable;
use Symfony\Contracts\EventDispatcher\Event;

final class LocalizationSettingsUpdateFailedEvent extends Event
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
