<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm\Event;

use Throwable;
use Symfony\Contracts\EventDispatcher\Event;

final class FailedToRunScenarioEvent extends Event
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
