<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal\Dispatcher\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class ModalClosedEvent extends Event
{
	private array $names;

	/**
	 * @param array $names
	 */
	public function __construct(array $names)
	{
		$this->names = $names;
	}

	/**
	 * @return array
	 */
	public function names(): array
	{
		return $this->names;
	}
}
