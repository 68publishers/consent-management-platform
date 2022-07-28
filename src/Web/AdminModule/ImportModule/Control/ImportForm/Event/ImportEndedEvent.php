<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportForm\Event;

use App\Application\Import\ImportState;
use Symfony\Contracts\EventDispatcher\Event;

final class ImportEndedEvent extends Event
{
	private ImportState $importState;

	/**
	 * @param \App\Application\Import\ImportState $importState
	 */
	public function __construct(ImportState $importState)
	{
		$this->importState = $importState;
	}

	/**
	 * @return \App\Application\Import\ImportState
	 */
	public function importState(): ImportState
	{
		return $this->importState;
	}
}
