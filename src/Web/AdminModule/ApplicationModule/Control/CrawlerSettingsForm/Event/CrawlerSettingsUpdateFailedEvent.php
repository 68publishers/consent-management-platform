<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\CrawlerSettingsForm\Event;

use Throwable;
use Symfony\Contracts\EventDispatcher\Event;

final class CrawlerSettingsUpdateFailedEvent extends Event
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
