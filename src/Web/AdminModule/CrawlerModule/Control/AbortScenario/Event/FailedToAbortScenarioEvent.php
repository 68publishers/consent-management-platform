<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\AbortScenario\Event;

use Throwable;
use Symfony\Contracts\EventDispatcher\Event;

final class FailedToAbortScenarioEvent extends Event
{
	private Throwable $error;

	public function __construct(Throwable $error)
	{
		$this->error = $error;
	}

	public function getError(): Throwable
	{
		return $this->error;
	}
}
