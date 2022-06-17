<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\DeleteProject\Event;

use Throwable;
use Symfony\Contracts\EventDispatcher\Event;

final class ProjectDeletionFailedEvent extends Event
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
