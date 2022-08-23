<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Control\ExportForm\Event;

use Throwable;
use Symfony\Contracts\EventDispatcher\Event;

final class ExportFormProcessingFailedEvent extends Event
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
